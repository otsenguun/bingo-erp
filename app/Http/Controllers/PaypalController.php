<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Services\TenantService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Srmklive\PayPal\Facades\PayPal;
use App\Notifications\SubscriptionSuccessNotification;

class PaypalController extends Controller
{
    /**
     * @throws \Throwable
     */
    public function success(Request $request, TenantService $tenantService)
    {
        $provider = PayPal::setProvider();
        $accessToken = $provider->getAccessToken();
        $provider->setAccessToken($accessToken);
        $paypalOrder = $provider->capturePaymentOrder($request->get('token'));

        if (isset($paypalOrder['error'])) {
            // Handle case when error is present in the array
            throw new \Exception(collect($paypalOrder['error']['details'])->pluck(['description'])->join(','));
        }

        if ($paypalOrder['status'] !== 'COMPLETED') {
            throw new \Exception('Payment not completed because of: '.$paypalOrder);
        }

        if (!isset($paypalOrder['purchase_units'][0]['reference_id'])) {
            // Handle case when reference ID is not present in the array
            Log::channel('payment')->error('Paypal IPN: Reference ID not found in the array', [
                'paypalOrder' => $paypalOrder,
                'request' => $request->all(),
                'user' => auth()->id() ?? 'Guest from IP: '.$request->ip(),
            ]);

            throw new \Exception('Reference ID not found in the array');
        }

        // Use $referenceId for further processing
        $clientReferenceId = $paypalOrder['purchase_units'][0]['reference_id'];
        $amount = $paypalOrder['purchase_units'][0]['payments']['captures'][0]['amount']['value'];
        $currency = $paypalOrder['purchase_units'][0]['payments']['captures'][0]['amount']['currency_code'];

        $payment = Payment::where('system_trx_id', $clientReferenceId)->firstOrFail();

        DB::beginTransaction();

        $tenant = $payment->tenant;
        $plan = $payment->plan;

        $tenant->update([
            'trial_ends_at' => null,
            'plan_id' => $plan->id,
            'plan_ends_at' => now()->addMonths($payment->quantity),
        ]);

        $subscription = Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'approved_by_id' => auth()->id(),
            'quantity' => $payment->quantity,
            'ends_at' => now()->addMonths($payment->quantity),
        ]);

        $payment->update([
            'subscription_id' => $subscription->id,
            'status' => 'success',
            'gateway_trx_id' => $paypalOrder['id'],
            'currency' => $currency,
            'data' => $paypalOrder,
        ]);

        DB::commit();

        $tenant->notify(new SubscriptionSuccessNotification());

        [, $domainWithHost] = $tenantService->getDomainWithHost($payment->tenant);
        $status = 'success';
        $message = 'Payment was successful.';
        return redirect()->away($domainWithHost . '/dashboard?payment_status=' . $status . '&message=' . urlencode($message));
    }

    public function cancel(Request $request, TenantService $tenantService)
    {
        $provider = PayPal::setProvider();
        $accessToken = $provider->getAccessToken();
        $provider->setAccessToken($accessToken);
        $orderId = $request->input('token');
        $orderDetails = $provider->showOrderDetails($orderId);
        $clientReferenceId = $orderDetails['purchase_units'][0]['reference_id'];
        $payment = Payment::where('system_trx_id', $clientReferenceId)->firstOrFail();
        [, $domainWithHost] = $tenantService->getDomainWithHost($payment->tenant);
        $status = 'cancelled';
        $message = 'Payment was unsuccessful.';
        return redirect()->away($domainWithHost . '/dashboard?payment_status=' . $status . '&message=' . urlencode($message));
    }
}