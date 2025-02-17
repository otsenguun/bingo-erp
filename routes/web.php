<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DebugController;
use App\Http\Controllers\PaypalController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\PaystackController;
use App\Http\Controllers\RazorpayController;
use App\Http\Controllers\CentralAppController;
use App\Http\Controllers\Central\ExportController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\NewsletterSubscriptionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
if (! app()->isProduction()) {
    Route::group(['prefix' => '/debug'], function () {
        Route::get('/version', [DebugController::class, 'version']);
    });
}

// display system info
Route::get('/system-info', function () {
    return phpinfo();
})->name('systemInfo');

Route::get('stripe/cancel', [StripeController::class, 'cancel'])->name('stripe.cancel');
Route::get('stripe/success', [StripeController::class, 'success'])->name('stripe.success');

Route::get('paypal/cancel', [PaypalController::class, 'cancel'])->name('paypal.cancel');
Route::get('paypal/success', [PaypalController::class, 'success'])->name('paypal.success');

Route::get('paystack/success', [PaystackController::class, 'success'])->name('paystack.callback');

Route::get('razorpay/success', [RazorpayController::class, 'success'])->name('razorpay.callback');

Route::get('/newsletter-confirm', [NewsletterSubscriptionController::class, 'confirm'])->name('newsletter-confirm');

Route::group(['middleware' => ['is_verified', 'need_to_install']], function () {
    Route::get('email/verify/{tenant}', [VerificationController::class, 'verify'])->name('verification.verify');
    Route::get('/tenants/pdf', [ExportController::class, 'tenantsPdf'])->name('tenants.pdf');
    Route::get('/tenants/export/excel', [ExportController::class, 'tenantsExportExcel'])->name('tenants.export.excel');
    Route::get('/domain-requests', CentralAppController::class)->name('domain-requests.index');

    Route::get('/subscription-requests', CentralAppController::class)->name('subscription-requests.index');

    // Central Routes
    Route::group(['as' => 'central.'], function () {
        Route::group(['middleware' => 'auth:sanctum'], function () {
            // spa view
            Route::get('/dashboard', CentralAppController::class)->name('dashboard.index');
        });
    });

    // SPA Routes
    Route::get('/{path}', CentralAppController::class)->where('path', '^(?!.*(?:api|storage)).*$');
});