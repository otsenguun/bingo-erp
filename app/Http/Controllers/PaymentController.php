<?php

namespace App\Http\Controllers;

use Stripe\Stripe;

use App\Models\Plan;
use Razorpay\Api\Api;
use App\Models\Payment;
use App\Models\GeneralSetting;
use App\Models\CentralCurrency;
use Illuminate\Support\Facades\DB;
use Srmklive\PayPal\Facades\PayPal;
use Illuminate\Support\Facades\Auth;
use Stripe\Checkout\Session as StripeSession;
use Unicodeveloper\Paystack\Facades\Paystack;
use App\Http\Resources\CentralCurrencyResource;

class PaymentController extends Controller
{
    // central panel active currency
    public function centralActiveCurrency()
    {
        $activeCurrencyID = tenancy()->central(fn() => GeneralSetting::where('key', 'default_currency')->first()->value);

        $currency = tenancy()->central(fn() => CentralCurrency::where('id', $activeCurrencyID)->first());
        return $currency;
    }

    // central Nigerian Naira (NGN) currency
    public function centralNGNCurrency()
    {
        $currency = tenancy()->central(fn() => CentralCurrency::where('code', 'NGN')->first());
        return $currency;
    }

    // central Indian Rupee (INR) currency
    public function centralINRCurrency()
    {
        $currency = tenancy()->central(fn() => CentralCurrency::where('code', 'INR')->first());
        return $currency;
    }

    public function currencyConvert()
    {
        $activeCurrencyID = tenancy()->central(fn() => GeneralSetting::where('key', 'default_currency')->first()->value);
        $currency = tenancy()->central(fn() => CentralCurrency::where('id', $activeCurrencyID)->first());

        $endpoint = 'convert';
        $access_key = env('EXCHANGE_RATES_API_KEY');

        $from = 'USD';
        $to = $currency->code; // central active/default currency
        $amount = 1;

        $ch = curl_init('http://api.exchangerate.host/' . $endpoint . '?access_key=' . $access_key . '&from=' . $from . '&to=' . $to . '&amount=' . $amount . '');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $json = curl_exec($ch);
        curl_close($ch);

        $conversionResult = json_decode($json, true);
        return $conversionResult['result'];
    }

    public function pay($planId, $quantity, $paymentMethod, $payerPreferredCurrency, $selectedPlanType)
    {
        $EXCHANGE_LIVE_CURRENCY = env('EXCHANGE_LIVE_CURRENCY');
        $EXCHANGE_RATES_API_KEY = env('EXCHANGE_RATES_API_KEY');

        $planDiscount = tenancy()->central(fn() => GeneralSetting::where('key', 'plan_discount')->first()?->value);
        if ($selectedPlanType === "year") {
            $quantity = $quantity*12;
        }

        $centralActiveCurrency =  $this->centralActiveCurrency();
        if ($EXCHANGE_LIVE_CURRENCY && !empty($EXCHANGE_RATES_API_KEY)) {
            $rate = $this->currencyConvert();
        } else {
            $rate = $centralActiveCurrency->rate;
        }

        $tenant = tenant();

        $plan = tenancy()->central(function () use ($planId) {
            return Plan::findOrFail($planId);
        });

        DB::beginTransaction();

        $payment = tenancy()->central(function () use ($tenant, $plan, $quantity, $paymentMethod, $rate, $planDiscount, $selectedPlanType) {
            return Payment::create([
                'tenant_id' => $tenant->id,
                'plan_id' => $plan->id,
                'quantity' => $quantity,
                'method' => $paymentMethod,
                'currency' => $plan->currency,
                'amount' => $selectedPlanType === "month" ? $plan->amount / $rate : ($plan->amount - ($plan->amount * ($planDiscount / 100))) / $rate,
                'default_amount_rate' => $selectedPlanType === "month" ? $plan->amount : $plan->amount - ($plan->amount * ($planDiscount / 100)),
            ]);

            // add activity log
            activity()
            ->withProperties([
                'name' => $payment->system_trx_id,
                'code' => '[' . $payment->system_trx_id . ']',
                'event' => 'Create',
                'slug' => '/',
                'routeName' => 'payments.index'
            ])
            ->useLog('Payment Created')
            ->log('Payment Created');
        });

        // add activity log
        activity()
        ->causedBy(Auth::user())
        ->withProperties([
            'name' => $payment->system_trx_id,
            'code' => '[' . $payment->system_trx_id . ']',
            'event' => 'Create',
            'slug' => '/',
            'routeName' => 'settings.billing.payments'
        ])
        ->useLog('Payment Created')
        ->log('Payment Created');

        if ($paymentMethod === 'stripe') {
            Stripe::setApiKey(config('payment-methods.stripe.secret'));
            $unitAmount = $selectedPlanType === "month" ? number_format($plan->amount / $rate * 100, 0, '.', '') : number_format(($plan->amount - ($plan->amount * ($planDiscount / 100))) / $rate * 100, 0, '.', '');

            $session = StripeSession::create([
                'client_reference_id' => $payment->system_trx_id,
                'customer_email' => auth()->user()->email,
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => $plan->currency,
                            'unit_amount' => $unitAmount,
                            'product_data' => [
                                'name' => $plan->name,
                                'description' => $plan->description,
                            ],
                        ],
                        'quantity' => $quantity,
                    ],
                ],
                'mode' => 'payment',
                'success_url' => route('stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('stripe.cancel'),
            ]);

            return response()->json(['url' => $session->url]);
        } else if ($paymentMethod === 'paypal') {
            // Through facade. No need to import namespaces
            $provider = PayPal::setProvider();
            $accessToken = $provider->getAccessToken();
            $provider->setAccessToken($accessToken);
            $value = $selectedPlanType === "month" ? number_format($plan->amount / $rate * $quantity, 2, '.', '') : number_format(($plan->amount - ($plan->amount * ($planDiscount / 100))) / $rate * $quantity, 2, '.', '');
            $paypalOrder = $provider->createOrder([
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'amount' => [
                            'currency_code' => 'USD',
                            'value' => $value
                        ],
                        'reference_id' => $payment->system_trx_id,
                    ]
                ],
                'application_context' => [
                    'cancel_url' => route('paypal.cancel'),
                    'return_url' => route('paypal.success'),
                ]
            ]);

            // $paypalOrder_id = $paypalOrder['id'];
            $redirectUrl = $paypalOrder['links'][1]['href'];
            return response()->json(['url' => $redirectUrl]);
        } else if ($paymentMethod === 'paystack') {
            $centralNGNCurrencyRate = $this->centralNGNCurrency()->rate;
            $isMonthly = $selectedPlanType === "month";

            // Determine base amount based on whether a discount should be applied
            $baseAmount = $isMonthly ? $plan->amount : $plan->amount - ($plan->amount * ($planDiscount / 100));

            // Calculate the amount based on the active currency
            $amount = match ($centralActiveCurrency->code) {
                'NGN' => $baseAmount * $quantity * 100,
                'usd' => (int) ($baseAmount * $centralNGNCurrencyRate * $quantity * 100),
                default => (int) ($baseAmount / $rate * $centralNGNCurrencyRate * $quantity * 100),
            };

            $paymentData = [
                'amount' => $amount,
                'email' => auth()->user()->email,
                'callback_url' => route('paystack.callback'),
                'metadata' => json_encode([
                    'system_trx_id' => $payment->system_trx_id,
                ]),
            ];

            try {
            $redirectUrl = Paystack::getAuthorizationUrl($paymentData)->url;
            return response()->json(['url' => $redirectUrl], 200);
            } catch (\Exception $e) {
                return response()->json(['error' => 'The Paystack token has expired. Please try again.'], 500);
            }
        }else if ($paymentMethod === 'razorpay') {
            $centralINRCurrencyRate = $this->centralINRCurrency()->rate;
            $isMonthly = $selectedPlanType === "month";

            // Determine base amount based on whether a discount should be applied
            $baseAmount = $isMonthly ? $plan->amount : $plan->amount - ($plan->amount * ($planDiscount / 100));

            // Calculate the amount based on the active currency
            $amount = match ($centralActiveCurrency->code) {
                'INR' => $baseAmount * $quantity * 100,
                'usd' => (int) ($baseAmount * $centralINRCurrencyRate * $quantity * 100),
                default => (int) ($baseAmount / $rate * $centralINRCurrencyRate * $quantity * 100),
            };

            $api = new Api(env('RAZORPAY_KEY_ID'), env('RAZORPAY_KEY_SECRET'));
            
            // Create a payment link
            $paymentData = $api->invoice->create([
                'type' => 'link',
                'amount' => $amount,
                'currency' => 'INR',
                'description' => 'Payment for order #'.uniqid(),
                'customer' => [
                    'name' => auth()->user()->name,
                    'email' => auth()->user()->email,
                ],
                'notify' => [
                    'sms' => false,
                    'email' => false,
                ],
                'callback_url' => route('razorpay.callback') . '?system_trx_id=' . $payment->system_trx_id,
                'callback_method' => 'get'
            ]);

            return response()->json(['url' => $paymentData->short_url], 200);
        }
        

        return $this->responseWithError('Payment method not supported');
    }
}
