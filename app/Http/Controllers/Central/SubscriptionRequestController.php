<?php

namespace App\Http\Controllers\Central;

use App\Models\Plan;
use App\Models\Tenant;
use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Models\SubscriptionRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PaymentController;
use App\Http\Resources\SubscriptionRequestResource;
use App\Notifications\SubscriptionSuccessNotification;

class SubscriptionRequestController extends Controller
{
    public function index(Request $request)
    {
        $subscriptionRequests = SubscriptionRequest::with(['plan', 'tenant', 'statusUpdatedBy'])
            ->orderBy('status')
            ->latest()
            ->paginate($request->perPage);

        return SubscriptionRequestResource::collection($subscriptionRequests);
    }

    /**
     * @throws \Throwable
     */
    public function update(SubscriptionRequest $subscriptionRequest, Request $request)
    {
        $EXCHANGE_LIVE_CURRENCY = env('EXCHANGE_LIVE_CURRENCY');
        $EXCHANGE_RATES_API_KEY = env('EXCHANGE_RATES_API_KEY');

        $paymentController = new PaymentController();

        $centralActiveCurrency =  $paymentController->centralActiveCurrency();
        if ($EXCHANGE_LIVE_CURRENCY && !empty($EXCHANGE_RATES_API_KEY)) {
            $rate = $paymentController->currencyConvert();
        } else {
            $rate = $centralActiveCurrency->rate;
        }


        $validated = $request->validate([
            'status' => ['required', Rule::in([
                SubscriptionRequest::STATUS_ACCEPTED,
                SubscriptionRequest::STATUS_REJECTED,
            ])],
        ]);

        $subscriptionRequest->update([
            'status_updated_by_id' => auth()->id(),
            'status' => $validated['status'],
        ]);

        // give subscription if accepted
        if ($validated['status'] === SubscriptionRequest::STATUS_ACCEPTED) {
            $tenant = Tenant::findOrFail($subscriptionRequest->tenant_id);
            $plan = Plan::findOrFail($subscriptionRequest->plan_id);

            DB::beginTransaction();

            $tenant->update([
                'trial_ends_at' => null,
                'plan_id' => $plan->id,
                'plan_ends_at' => now()->addMonths($subscriptionRequest->quantity),
            ]);

            $subscription = Subscription::updateOrCreate(
                ['subscription_request_id' => $subscriptionRequest->id],
                [
                    'tenant_id' => $tenant->id,
                    'plan_id' => $plan->id,
                    'approved_by_id' => auth()->id(),
                    'quantity' => $subscriptionRequest->quantity,
                    'ends_at' => now()->addMonths($subscriptionRequest->quantity),
                ]
            );

            Payment::create([
                'tenant_id' => $tenant->id,
                'plan_id' => $plan->id,
                'subscription_id' => $subscription->id,
                'status' => 'success',
                'quantity' => $subscriptionRequest->quantity,
                'method' => 'manual',
                'currency' => $centralActiveCurrency->code,
                'amount' => $plan->amount / $rate,
                'default_amount_rate' => $plan->amount,
            ]);

            DB::commit();

            $tenant->notify(new SubscriptionSuccessNotification());
        }

        // add activity log
        activity()
        ->causedBy(Auth::user())
        ->withProperties([
            'name' => '',
            'code' => '[' . $subscriptionRequest->transaction_id . ']',
            'event' => 'Update',
            'slug' => '/',
            'routeName' => 'subscription-requests.index'
        ])
        ->useLog('Subscription Request Updated')
        ->log('Subscription Request Updated');

        return $this->responseWithSuccess('Subscription request updated successfully');
    }

    public function destroy(SubscriptionRequest $subscriptionRequest)
    {
        if ($subscriptionRequest->status === SubscriptionRequest::STATUS_PENDING) {
            $subscriptionRequest->delete();

            return $this->responseWithSuccess('Deleted successfully.');
        }

        return $this->responseWithError('Can not delete accepted or rejected requests.');
    }
}