<?php

namespace App\Listeners;

use App\Models\Tenant;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Events\WebhookReceived;
use Laravel\Cashier\Subscription;

class StripeEventListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        try {
            if ($event->payload['type'] == 'customer.subscription.deleted') {
                // get subscription id
                $subscriptionId = $event->payload['data']['object']['id'];
                // get subscription object
                $subscription = Subscription::where('stripe_id', $subscriptionId)->firstOrFail();
                // get tenant
                $tenantId = $subscription->tenant_id;
                $tenant = Tenant::where('id', $tenantId)->firstOrFail();
                // delete tenant plan id
                $tenant->update(['plan_id' => null]);
                // delete all payment methods
                $tenant->deletePaymentMethods();
            }
        }
        catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
