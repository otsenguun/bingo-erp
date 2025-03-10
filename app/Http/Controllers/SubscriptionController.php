<?php

namespace App\Http\Controllers;


use App\Models\Plan;
use Illuminate\Http\Request;
use App\Models\GeneralSetting;
use App\Models\CentralCurrency;
use App\Http\Resources\Plan\PlanResource;
use App\Http\Controllers\PaymentController;
use App\Http\Resources\CentralCurrencyResource;

class SubscriptionController extends Controller
{
    public function plans()
    {
        $plans = tenancy()->central(fn () => Plan::all());

        return response()->json([
            'data' => PlanResource::collection($plans),
        ]);
    }

    public function centralActiveCurrencyRate()
    {
        $activeCurrencyID = tenancy()->central(fn () => GeneralSetting::where('key', 'default_currency')->first()->value);
        $currency = tenancy()->central(fn () => CentralCurrency::where('id', $activeCurrencyID)->first());
        return $currency;
    }

    public function currencyExchangeRate(Request $request)
    {
        $quantity = $request->query('quantity');
        $plan = tenancy()->central(fn () => Plan::find($request->query('plan_id')));
        $paymentController = new PaymentController();

        $EXCHANGE_LIVE_CURRENCY = env('EXCHANGE_LIVE_CURRENCY');
        $EXCHANGE_RATES_API_KEY = env('EXCHANGE_RATES_API_KEY');

        $centralActiveCurrency =  $this->centralActiveCurrencyRate();
        if ($EXCHANGE_LIVE_CURRENCY && !empty($EXCHANGE_RATES_API_KEY)) {
            $rate = $paymentController->currencyConvert();
            $basedOn = "Based on LIVE currency exchange rate";
        } else {
            $rate = $centralActiveCurrency->rate;
            $basedOn = "Based on Owner defined currency exchange rate";
        }

        $total = round($plan->amount * $quantity / $rate, 2);

        return response()->json([
            'rate' => $rate,
            'basedOn' => $basedOn,
            'total' => $total,
        ]);
    }

    public function centralActiveCurrency()
    {
        $activeCurrencyID = tenancy()->central(fn () => GeneralSetting::where('key', 'default_currency')->first()->value);

        $currency = tenancy()->central(fn () => CentralCurrency::where('id', $activeCurrencyID)->first());

        return response()->json([
            'data' => new CentralCurrencyResource($currency),
        ]);
    }

    public function getCentralPlanDiscount()
    {
        $planDiscount = tenancy()->central(fn() => GeneralSetting::where('key', 'plan_discount')->first()?->value);
        return response()->json([
            'data' => $planDiscount,
        ]);
    }

    // TODO: show this when tenant want to switch plan
    // public function switchPlans()
    // {
    //     $planId = tenant()->plan_id;
    //     $plans = tenancy()->central(fn() =>
    //     PlanResource::collection(Plan::with('items')->whereNot('id', $planId)->get()));
    //
    //     return response()->json([
    //         'data' => $plans,
    //     ]);
    // }

    /**
     * Creates an intent for payment, so we can capture the payment
     * method for the user.
     *
     * @return \Stripe\SetupIntent
     */
    public function getSetupIntent()
    {
        return tenant()->createSetupIntent();
    }

    /**
     * Returns the payment methods the user has saved
     */
    public function getPaymentMethods()
    {
        $tenant = tenant();

        $methods = [];

        if ($tenant->hasPaymentMethod()) {
            foreach ($tenant->paymentMethods() as $method) {
                $methods[] = [
                    'id' => $method->id,
                    'brand' => $method->card->brand,
                    'last_four' => $method->card->last4,
                    'exp_month' => $method->card->exp_month,
                    'exp_year' => $method->card->exp_year,
                ];
            }
        }

        return response()->json($methods);
    }

    /**
     * Adds a payment method to the current user.
     *
     * @param  Request  $request  The request data from the user.
     */
    public function postPaymentMethods(Request $request)
    {
        $tenant = tenant();
        $paymentMethodID = $request->payment_method;

        if ($tenant->stripe_id == null) {
            $tenant->createAsStripeCustomer();
        }

        $tenant->addPaymentMethod($paymentMethodID);
        $tenant->updateDefaultPaymentMethod($paymentMethodID);

        return response()->json(null, 204);
    }

    /**
     * Removes a payment method for the current user.
     *
     * @param $paymentMethodId
     * @return \Illuminate\Http\JsonResponse
     */
    public function removePaymentMethods($paymentMethodId)
    {
        $tenant = tenant();

        $paymentMethods = $tenant->paymentMethods();

        foreach ($paymentMethods as $method) {
            if ($method->id == $paymentMethodId) {
                $method->delete();
                break;
            }
        }

        return response()->json([], 204);
    }

    public function currentSubscription()
    {
        return response()->json([
            'data' => tenant()->subscriptions,
        ]);
    }

    /**
     * Updates a subscription for the user
     *
     * @throws \Laravel\Cashier\Exceptions\SubscriptionUpdateFailure
     * @throws \Laravel\Cashier\Exceptions\IncompletePayment
     */
    public function createOrUpdateSubscription(Request $request)
    {
        $request->validate([
            'plan_id' => ['required', 'integer'],
            'payment_method_id' => ['nullable'],
        ]);

        $plan = tenancy()->central(function () use ($request) {
            return Plan::find($request->plan_id);
        });

        $tenant = tenant();

        $tenant->createOrGetStripeCustomer();

        $currentPlan = tenancy()->central(function ($tenant) {
            return Plan::find($tenant->plan_id);
        });

        if (!is_null($currentPlan) && $tenant->subscribed('main')) {
            $subscription = tenant()->subscription('main')->swap($plan->api_id);
        } else {
            if (empty($request->payment_method_id)) {
                return $this->responseWithError('Payment method id required for new subscription');
            }

            $payment_method_id = $request->payment_method_id;
            $tenant->updateDefaultPaymentMethod($payment_method_id);
            $subscription = $tenant->newSubscription('main', $plan->api_id)
                ->create($payment_method_id);
        }

        $tenant->update([
            'trial_ends_at' => null,
            'plan_id' => $plan->id,
            'plan_ends_at' => $subscription->ends_at,
        ]);

        return response()->json([
            'message' => 'Subscribed successfully',
        ]);
    }

    public function cancelSubscription(Request $request)
    {
        $plan = tenancy()->central(function () use ($request) {
            return Plan::find($request->plan_id);
        });

        if (tenant()->subscribed('main')) {
            $subscription = tenant()->subscription('main')->cancel();

            tenant()->update([
                'plan_ends_at' => $subscription->ends_at,
            ]);

            return $this->responseWithSuccess('Subscription cancelled successfully');
        }

        return $this->responseWithError('No subscription found.');
    }

    public function resumeSubscription(Request $request)
    {
        $plan = tenancy()->central(function () use ($request) {
            return Plan::find($request->plan_id);
        });

        if (tenant()->subscribed('main')) {
            tenant()->subscription('main')->resume();

            tenant()->update([
                'plan_ends_at' => null,
            ]);

            return $this->responseWithSuccess('Subscription resumed successfully');
        }

        return $this->responseWithError('No subscription found.');
    }
}
