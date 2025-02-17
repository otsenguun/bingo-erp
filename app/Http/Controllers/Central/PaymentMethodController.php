<?php

namespace App\Http\Controllers\Central;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Jackiedo\DotenvEditor\Facades\DotenvEditor;

class PaymentMethodController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index()
    {
        $editor = DotenvEditor::load();

        $data['MANUAL_PAYMENT_IS_ACTIVE'] = $editor->getKey('MANUAL_PAYMENT_IS_ACTIVE');
        $data['MANUAL_PAYMENT_NOTE'] = $editor->getKey('MANUAL_PAYMENT_NOTE');
        $data['STRIPE_IS_ACTIVE'] = $editor->getKey('STRIPE_IS_ACTIVE');
        $data['STRIPE_KEY'] = $editor->getKey('STRIPE_KEY');
        $data['STRIPE_SECRET'] = $editor->getKey('STRIPE_SECRET');
        $data['STRIPE_WEBHOOK_SECRET'] = $editor->getKey('STRIPE_WEBHOOK_SECRET');


        $data['PAYPAL_IS_ACTIVE'] = $editor->getKey('PAYPAL_IS_ACTIVE');
        $data['PAYPAL_MODE'] = $editor->getKey('PAYPAL_MODE');

        $data['EXCHANGE_LIVE_CURRENCY'] = $editor->getKey('EXCHANGE_LIVE_CURRENCY');
        $data['EXCHANGE_RATES_API_KEY'] = $editor->getKey('EXCHANGE_RATES_API_KEY');

        if ($data['PAYPAL_MODE']['value'] === 'sandbox') {
            $data['PAYPAL_CLIENT_ID'] = $editor->getKey('PAYPAL_SANDBOX_CLIENT_ID');
            $data['PAYPAL_CLIENT_SECRET'] = $editor->getKey('PAYPAL_SANDBOX_CLIENT_SECRET');
        } else {
            $data['PAYPAL_CLIENT_ID'] = $editor->getKey('PAYPAL_LIVE_CLIENT_ID');
            $data['PAYPAL_CLIENT_SECRET'] = $editor->getKey('PAYPAL_LIVE_CLIENT_SECRET');
        }

        $data['PAYSTACK_IS_ACTIVE'] = $editor->getKey('PAYSTACK_IS_ACTIVE');
        $data['PAYSTACK_PUBLIC_KEY'] = $editor->getKey('PAYSTACK_PUBLIC_KEY');
        $data['PAYSTACK_SECRET_KEY'] = $editor->getKey('PAYSTACK_SECRET_KEY');
        $data['MERCHANT_EMAIL'] = $editor->getKey('MERCHANT_EMAIL');

        $data['RAZORPAY_IS_ACTIVE'] = $editor->getKey('RAZORPAY_IS_ACTIVE');
        $data['RAZORPAY_KEY_ID'] = $editor->getKey('RAZORPAY_KEY_ID');
        $data['RAZORPAY_KEY_SECRET'] = $editor->getKey('RAZORPAY_KEY_SECRET');

        return $this->responseWithSuccess('Data retrieved successfully', $data);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $editor = DotenvEditor::load();
        $editor->setKey('MANUAL_PAYMENT_IS_ACTIVE', $request->integer('MANUAL_PAYMENT_IS_ACTIVE'));
        $editor->setKey('MANUAL_PAYMENT_NOTE', $request->input('MANUAL_PAYMENT_NOTE'));
        $editor->setKey('STRIPE_IS_ACTIVE', $request->integer('STRIPE_IS_ACTIVE'));
        $editor->setKey('STRIPE_KEY', $request->input('STRIPE_KEY'));
        $editor->setKey('STRIPE_SECRET', $request->input('STRIPE_SECRET'));
        $editor->setKey('STRIPE_WEBHOOK_SECRET', $request->input('STRIPE_WEBHOOK_SECRET'));

        $editor->setKey('PAYPAL_IS_ACTIVE', $request->integer('PAYPAL_IS_ACTIVE'));
        $editor->setKey('PAYPAL_MODE', $request->input('PAYPAL_MODE'));

        $editor->setKey('EXCHANGE_LIVE_CURRENCY', $request->integer('EXCHANGE_LIVE_CURRENCY'));
        $editor->setKey('EXCHANGE_RATES_API_KEY', $request->input('EXCHANGE_RATES_API_KEY'));

        if ($request->input('PAYPAL_MODE') === 'sandbox') {
            $editor->setKey('PAYPAL_SANDBOX_CLIENT_ID', $request->input('PAYPAL_CLIENT_ID'));
            $editor->setKey('PAYPAL_SANDBOX_CLIENT_SECRET', $request->input('PAYPAL_CLIENT_SECRET'));
        } else {
            $editor->setKey('PAYPAL_LIVE_CLIENT_ID', $request->input('PAYPAL_CLIENT_ID'));
            $editor->setKey('PAYPAL_LIVE_CLIENT_SECRET', $request->input('PAYPAL_CLIENT_SECRET'));
        }

        $editor->setKey('PAYSTACK_IS_ACTIVE', $request->input('PAYSTACK_IS_ACTIVE'));
        $editor->setKey('PAYSTACK_PUBLIC_KEY', $request->input('PAYSTACK_PUBLIC_KEY'));
        $editor->setKey('PAYSTACK_SECRET_KEY', $request->input('PAYSTACK_SECRET_KEY'));
        $editor->setKey('MERCHANT_EMAIL', $request->input('MERCHANT_EMAIL'));

        $editor->setKey('RAZORPAY_IS_ACTIVE', $request->input('RAZORPAY_IS_ACTIVE'));
        $editor->setKey('RAZORPAY_KEY_ID', $request->input('RAZORPAY_KEY_ID'));
        $editor->setKey('RAZORPAY_KEY_SECRET', $request->input('RAZORPAY_KEY_SECRET'));

        $editor->save();

        // add activity log
        activity()
        ->causedBy(Auth::user())
        ->withProperties([
            'name' => '',
            'code' =>  '',
            'event' => 'Update',
            'slug' => '/',
            'routeName' => 'advanced-settings'
        ])
        ->useLog('Payment Methods Settings Updated')
        ->log('Payment Methods Settings Updated');

        return $this->responseWithSuccess('Updated successfully!');
    }
}