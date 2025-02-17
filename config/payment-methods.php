<?php

return [
    /**
     * List of payment methods.
     */

    'manual' => [
        'is_active' => env('MANUAL_PAYMENT_IS_ACTIVE', true),
        'name' => 'Manual Payment',
        'identifier' => 'manual',
        'description' => 'Manual Payment',
        'note' => env('MANUAL_PAYMENT_NOTE'),
    ],

    'stripe' => [
        'is_active' => env('STRIPE_IS_ACTIVE', true),
        'name' => 'Stripe',
        'identifier' => 'stripe',
        'description' => 'Stripe Payment Gateway',

        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],

    'paypal' => [
        'is_active' => env('PAYPAL_IS_ACTIVE', true),
        'name' => 'Paypal',
        'identifier' => 'paypal',
        'description' => 'Paypal Payment Gateway',
    ],

    'paystack' => [
        'is_active' => env('PAYSTACK_IS_ACTIVE', true),
        'name' => 'Paystack',
        'identifier' => 'paystack',
        'description' => 'Paystack Payment Gateway',
    ],

    'razorpay' => [
        'is_active' => env('RAZORPAY_IS_ACTIVE', true),
        'name' => 'Razorpay',
        'identifier' => 'razorpay',
        'description' => 'Razorpay Payment Gateway',
    ],
];
