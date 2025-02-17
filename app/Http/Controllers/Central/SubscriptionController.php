<?php

namespace App\Http\Controllers\Central;

use App\Models\Subscription;
use App\Http\Controllers\Controller;


class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = Subscription::with(['plan', 'tenant', 'approvedBy'])
            ->latest()
            ->paginate();

        return $subscriptions;
    }
}