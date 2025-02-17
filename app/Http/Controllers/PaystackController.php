<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Services\TenantService;
use Illuminate\Support\Facades\DB;
use Unicodeveloper\Paystack\Facades\Paystack;
use App\Notifications\SubscriptionSuccessNotification;

class PaystackController extends Controller
{
    public function success(TenantService $tenantService)
    {
        $paymentDetails = Paystack::getPaymentData();

        $metadata = $paymentDetails['data']['metadata'];
        $currency = $paymentDetails['data']['currency'];
        $systemTrxId = $metadata['system_trx_id'];

        if ($paymentDetails['status'] == true) {
            try {
                DB::beginTransaction();

                $payment = Payment::where('system_trx_id', $systemTrxId)->firstOrFail();

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
                    'gateway_trx_id' => $paymentDetails['data']['id'],
                    'currency' => $currency,
                    'data' => $paymentDetails,
                ]);

                DB::commit();

                $tenant->notify(new SubscriptionSuccessNotification());

                [, $domainWithHost] = $tenantService->getDomainWithHost($payment->tenant);
                $status = 'success';
                $message = 'Payment was successful.';
                return redirect()->away($domainWithHost . '/dashboard?payment_status=' . $status . '&message=' . urlencode($message));
            } catch (Exception $e) {
                DB::rollback();
                return response()->json(['error' => 'Something went wrong'], 500);
            }
        }else{
            $payment = Payment::where('system_trx_id', $systemTrxId)->firstOrFail();
            [, $domainWithHost] = $tenantService->getDomainWithHost($payment->tenant);
            $status = 'cancelled';
            $message = 'Payment was unsuccessful.';
            return redirect()->away($domainWithHost . '/dashboard?payment_status=' . $status . '&message=' . urlencode($message));
        }
    }
}
