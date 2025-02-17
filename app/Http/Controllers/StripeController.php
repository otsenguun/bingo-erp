<?php

namespace App\Http\Controllers;


use Stripe\Stripe;
use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Services\TenantService;
use Illuminate\Support\Facades\DB;
use Stripe\Checkout\Session as StripeSession;
use App\Notifications\SubscriptionSuccessNotification;

class StripeController extends Controller
{
    /**
     * @throws \Throwable
     */
    public function success(Request $request, TenantService $tenantService)
    {
        if (! $request->get('session_id')) {
            throw new \Exception('Session ID missing.');
        }

        Stripe::setApiKey(config('payment-methods.stripe.secret'));

        $response = StripeSession::retrieve($request->get('session_id'));

        // Use $clientReferenceId for further processing
        $clientReferenceId = $response['client_reference_id'];
        $amount =  $response['amount_total'] / 100;
        $currency = $response['currency'];

        DB::beginTransaction();

        $payment = Payment::where('system_trx_id', $clientReferenceId)->firstOrFail();

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
            'gateway_trx_id' => $response['id'],
            'currency' => $currency,
            'data' => $response,
        ]);

        DB::commit();

        $tenant->notify(new SubscriptionSuccessNotification());

        [, $domainWithHost] = $tenantService->getDomainWithHost($payment->tenant);
        $status = 'success';
        $message = 'Payment was successful.';
        return redirect()->away($domainWithHost . '/dashboard?payment_status=' . $status . '&message=' . urlencode($message));
    }

    public function cancel()
    {
        return redirect('/login');
    }
}