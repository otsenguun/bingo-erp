<?php

namespace App\Http\Controllers;

use Exception;
use Razorpay\Api\Api;
use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Services\TenantService;
use Illuminate\Support\Facades\DB;
use App\Notifications\SubscriptionSuccessNotification;

class RazorpayController extends Controller
{
    public function success(TenantService $tenantService, Request $request)
    {
        $system_trx_id = $request->query('system_trx_id');
        $api = new Api(env('RAZORPAY_KEY_ID'), env('RAZORPAY_KEY_SECRET'));
        $paymentId = $request->get('razorpay_payment_id');

        if (!$paymentId) {
            return response()->json(['error' => 'Payment ID not provided'], 400);
        }

        // Fetch the payment details
        $payment = $api->payment->fetch($paymentId);

        if ($payment->status === 'captured') {
            $paymentDetails = [
                'id' => $payment->id,
                'status' => $payment->status,
                'amount' => $payment->amount / 100,
                'currency' => $payment->currency,
                'method' => $payment->method,
                'description' => $payment->description,
                'created_at' => $payment->created_at,
            ];

            try {
                DB::beginTransaction();

                $payment = Payment::where('system_trx_id', $system_trx_id)->firstOrFail();

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
                    'gateway_trx_id' => $paymentDetails['id'],
                    'currency' => $paymentDetails['currency'],
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

            return response()->json($paymentDetails, 200);
        } else {
            $payment = Payment::where('system_trx_id', $system_trx_id)->firstOrFail();
            [, $domainWithHost] = $tenantService->getDomainWithHost($payment->tenant);
            $status = 'cancelled';
            $message = 'Payment was unsuccessful.';
            return redirect()->away($domainWithHost . '/dashboard?payment_status=' . $status . '&message=' . urlencode($message));
        }
    }
}
