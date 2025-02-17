<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\SubscriptionRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Notification;
use App\Http\Resources\SubscriptionRequestResource;
use App\Http\Requests\StoreSubscriptionRequestRequest;
use App\Notifications\NewSubscriptionRequestNotification;

class SubscriptionRequestController extends Controller
{
    /**
     * @return mixed
     */
    public function index(Request $request)
    {
        $tenant = tenant();
        $subscriptionRequests = tenancy()->central(function () use ($tenant, $request) {
            return SubscriptionRequest::with(['plan', 'tenant', 'statusUpdatedBy'])
                ->where('tenant_id', $tenant->id)
                ->latest()
                ->paginate($request->perPage);
        });
        return SubscriptionRequestResource::collection($subscriptionRequests);
    }

    /**
     * @param StoreSubscriptionRequestRequest $request
     * @return JsonResponse
     */
    public function store(StoreSubscriptionRequestRequest $request)
    {
        $tenant = tenant();

        if ($request->input('payment_method') !== 'manual') {
            $paymentController = new PaymentController();
            return $paymentController->pay($request->input('plan_id'), $request->input('quantity'), $request->input('payment_method'), $request->input('currency'), $request->input('selectedPlanType'));
        }

        try {
            $subscriptionRequestMessage = tenancy()->central(function () use ($tenant, $request) {
                $documentPath = null;
                if ($request->hasFile('document_path')) {
                    $documentPath = $request->file('document_path')->store('subscription-request', 'public');
                }

                $subscriptionRequest = SubscriptionRequest::create([
                    'tenant_id' => $tenant->id,
                    'document_path' => $documentPath,
                ]+ $request->safe()->except('document_path', 'payment_method'));

                    // notify admin
                    $admins = User::where('account_role', 1)->get();
                    Notification::send($admins, new NewSubscriptionRequestNotification($subscriptionRequest, $tenant));

                    // add activity log
                    activity()
                    ->withProperties([
                        'name' => $request->transaction_id,
                        'code' => '[' . $request->transaction_id . ']',
                        'event' => 'Create',
                        'slug' => '/',
                        'routeName' => 'subscription-requests.index'
                    ])
                    ->useLog('Subscription Request Created')
                    ->log('Subscription Request Created');

                    return [
                        'message' => 'Successfully requested subscription.',
                        'code' => 201,
                    ];
            });

            // add activity log
            activity()
            ->causedBy(Auth::user())
            ->withProperties([
                'name' => $request->transaction_id,
                'code' => '[' . $request->transaction_id . ']',
                'event' => 'Create',
                'slug' => '/',
                'routeName' => 'settings.billing.subscription-requests'
            ])
            ->useLog('Subscription Request Created')
            ->log('Subscription Request Created');

            return $this->responseWithSuccess(
                $subscriptionRequestMessage['message'],
                [],
                $subscriptionRequestMessage['code'],
            );
        } catch (\Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }
}