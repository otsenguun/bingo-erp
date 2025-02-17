<?php

namespace App\Http\Controllers\Central;

use Exception;
use Carbon\Carbon;
use App\Models\Plan;
use App\Models\Tenant;
use App\Models\Payment;
use Akaunting\Money\Money;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use Stancl\Tenancy\Database\Models\Domain;
use App\Http\Controllers\PaymentController;

class DashboardController extends Controller
{
    public function index($summaryType)
    {
        // today
        // last_7_days
        // this_month
        // this_year

        // $stripe = Stripe::make();
        //
        // $balancesCount = $stripe ? $stripe->balance()->current() : 0;
        // $pending = Money::USD($balancesCount['pending'][0]['amount']);
        // $available = Money::USD($balancesCount['available'][0]['amount']);

        // TODO: stripe payment count
        $balancesCount = 0;
        // $pending = Money::USD(0);
        // $available = Money::USD(0);

        $paymentController = new PaymentController();
        $centralActiveCurrency = $paymentController->centralActiveCurrency();

        if ($summaryType == 'today') {

            $todaySubscribedAmounts = Payment::where('status', 'success')->whereDate('created_at', today())->get();
            $totalSubscribedAmount = $todaySubscribedAmounts->sum(function ($payment) {
                return $payment->quantity * $payment->amount;
            });

            $todayPendingAmounts = Payment::where('status', 'pending')->whereDate('created_at', today())->get();
            $totalPendingAmount = $todayPendingAmounts->sum(function ($payment) {
                return $payment->quantity * $payment->default_amount_rate;
            });

            $tenantsCount = Tenant::whereNotNull('data->email_verified_at')
                ->whereDate('created_at', today())
                ->count();

            $domainsCount = Domain::whereDate('created_at', today())
                ->count();

            $onTrialTenantsCount = Tenant::whereNotNull('trial_ends_at')
                ->whereDate('created_at', today())
                ->count();
        } else {
            if ($summaryType == 'last_7_days') {
                $startDate = today()->subDays(7);
                $endDate = today();
            } else {
                if ($summaryType == 'this_month') {
                    $startDate = today()->startOfMonth();
                    $endDate = today()->endOfMonth();
                } else {
                    $startDate = today()->startOfYear();
                    $endDate = today()->endOfYear();
                }
            }

            $dateRangeSubscribedAmount = Payment::where('status', 'success')->whereBetween('created_at', [$startDate, $endDate])->get();
            $totalSubscribedAmount = $dateRangeSubscribedAmount->sum(function ($payment) {
                return $payment->quantity * $payment->amount;
            });

            $dateRangePendingAmount = Payment::where('status', 'pending')->whereBetween('created_at', [$startDate, $endDate])->get();
            $totalPendingAmount = $dateRangePendingAmount->sum(function ($payment) {
                return $payment->quantity * $payment->default_amount_rate;
            });

            $tenantsCount = Tenant::whereNotNull('data->email_verified_at')
                ->whereDate('trial_ends_at', '>', now())
                ->orWhere('trial_ends_at', null)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();

            $domainsCount = Domain::whereBetween('created_at', [$startDate, $endDate])
                ->count();

            $onTrialTenantsCount = Tenant::whereNotNull('trial_ends_at')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
        }

        // Format the amount based on the currency position
        $currencyPosition = $centralActiveCurrency->position;
        if ($currencyPosition === 'left') {
            $formattedPendingAmount = $centralActiveCurrency->symbol . number_format($totalPendingAmount, 2);
        } else {
            $formattedPendingAmount = number_format($totalPendingAmount, 2) . $centralActiveCurrency->symbol;
        }

        return $this->responseWithSuccess('Summary retrieved', [
            'items' => [
                [
                    'name'      => 'tenants_count',
                    'value'     => $tenantsCount,
                    'icon'      => 'fas fa-users',
                    'route'     => 'tenants.index',
                    'bgColor'   => 'bg-primary'
                ],
                [
                    'name'      => 'on_trial_tenants_count',
                    'value'     => $onTrialTenantsCount,
                    'icon'      => 'fas fa-user-friends',
                    'bgColor'   => 'bg-info'
                ],
                [
                    'name'      => 'pending_amount',
                    'value'     =>  $formattedPendingAmount,
                    'icon'      => 'fas fa-spinner',
                    'bgColor'   => 'bg-success'
                ],

                [
                    'name'      => 'available_amount',
                    'value'     => '$' . $totalSubscribedAmount,
                    'icon'      => 'fas fa-piggy-bank',
                    'bgColor'   => 'bg-olive'
                ],
            ],

            'type' => $summaryType,
        ]);
    }

    public function topPlans()
    {
        $plans = Plan::withCount('tenants')
            ->orderBy('tenants_count', 'desc')
            ->get();

        $names = $plans->pluck(['name']);

        $superPlans = $plans->map(function ($plan) {
            return [
                'name' => $plan->name,
                'value' => $plan->tenants_count,
            ];
        });

        return response()->json([
            'names' => $names,
            'plans' => $superPlans,
        ]);
    }

    public function transactionStatus()
    {
        $hasDataInCentralPaymentTable = Payment::count() > 0;
        return response()->json($hasDataInCentralPaymentTable);
    }

    // return top clients
    public function topClients()
    {
        $topClients = Tenant::withCount('subscriptions')
            ->orderBy('subscriptions_count', 'desc')->get();

        return $this->responseWithSuccess('Top clients retrieved', $topClients);
    }

    // update user profile
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        // validate request
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|min:3|unique:users,email,' . $user->id,
            'currentPassword' => $request->newPassword != null ? ['required', 'string', 'min:8', function ($attribute, $value, $fail) use ($user) {
                if (!Hash::check($value, $user->password)) {
                    return $fail(__('The current password is incorrect.'));
                }
            }] : 'nullable',

            'newPassword' => $request->currentPassword != null ? 'required|string|min:8|required_with:confirmPassword' : 'nullable',
            'confirmPassword' => $request->newPassword != null ? 'required|string|min:8|same:newPassword' : 'nullable',
        ]);

        try {

            $password = $user->password;
            if ($request->newPassword) {
                $password = bcrypt($request->newPassword);
            }

            $user->update([
                'name'      => $request->name,
                'email'     => $request->email,
                'password'  => $password,
            ]);

                // add activity log
                activity()
                ->causedBy(Auth::user())
                ->performedOn($user)
                ->withProperties([
                    'name' => '',
                    'code' =>  '',
                    'event' => 'Update',
                    'slug' => '/',
                    'routeName' => 'profile'
                ])
                ->useLog('Profile Updated')
                ->log('Profile Updated');

            return $this->responseWithSuccess('Profile updated successfully');
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    public function databaseBackup(Request $request)
    {
        $fileName = env('DB_DATABASE') . '_' . Carbon::now()->getTimestamp() . '.' . $request->input('format');
        try {
            Artisan::call('database:backup', ['fileName' => $fileName]);
        } catch (Exception $e) {
            echo 'Message: ' . $e->getMessage();
        }

        $pathToFile = storage_path() . '//backup/' . $fileName;

        $headers = [
            'Content-Description' => 'File Transfer',
            'Content-Type' => 'application/sql',
        ];

        return response()->download($pathToFile, $fileName, $headers);
    }
}
