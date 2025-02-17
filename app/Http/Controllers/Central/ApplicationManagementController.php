<?php

namespace App\Http\Controllers\Central;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Stancl\Tenancy\Contracts\Tenant;
use Illuminate\Support\Facades\Artisan;
use Jackiedo\DotenvEditor\Facades\DotenvEditor;


class ApplicationManagementController extends Controller
{
    public function getUpdateVersion()
    {
        $updatedVersion = config('app.nextAppVersion');

        return response()->json([
            'status' => 'success',
            'data' => $updatedVersion,
        ]);
    }

    public function updateApplication()
    {
        $editor = DotenvEditor::load();
        $currentAppVersion = $editor->getKey('VERSION')['value'];
        $refinedAppVersion = ltrim($currentAppVersion, 'v');

        $updatedVersion = config('app.nextAppVersion');
        if ($updatedVersion) {
            if ($refinedAppVersion == $updatedVersion) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'The application is up to date',
                ]);
            } else {
                try {
                    // for central
                    $centralMigrateExitCode = Artisan::call('migrate', ['--force' => true]);
                    $centralMigrateOutput = Artisan::output();

                    // for tenants
                    $tenantsMigrateExitCode = Artisan::call('tenants:migrate', ['--force' => true]);
                    $tenantsMigrateOutput = Artisan::output();

                    Artisan::call('db:seed', ['--class' => 'CentralSettingSeeder', '--force' => true]);

                    // Runs for each tenant
                    tenancy()->query()->cursor()->each(function ($tenant) {
                        tenancy()->initialize($tenant);

                        // Run the seeder within the tenant's database
                        Artisan::call('db:seed', ['--class' => 'TenantMenuSeeder', '--force' => true]);

                        // $this->addNewPermissions();

                        tenancy()->end();
                    });

                    $this->addNewPaymentGateways($editor);

                    // update the version value
                    $editor->setKey('VERSION', $updatedVersion);
                    $editor->save();

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Application updated successfully',
                        'central_migrate_exit_code' => $centralMigrateExitCode,
                        'central_migrate_output' => $centralMigrateOutput,
                        'tenants_migrate_exit_code' => $tenantsMigrateExitCode,
                        'tenants_migrate_output' => $tenantsMigrateOutput,
                    ]);
                } catch (\Exception $e) {
                    return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
                }
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!',
            ]);
        }
    }

    private function addNewPermissions()
    {
        $superAdminRole = DB::table('roles')->where('slug', 'super-admin')->first();
        if (!$superAdminRole) {
            throw new \Exception('Super Admin role not found');
        }

        $newPermissions = [
            // application update permissions
            [
                'name' => 'Update',
                'guard_name' => 'Application Management',
                'slug' => 'update-application',
            ],
        ];

        foreach ($newPermissions as $permissionData) {
            $permission = DB::table('permissions')->where('slug', $permissionData['slug'])->first();

            if (!$permission) {
                $permissionId = DB::table('permissions')->insertGetId($permissionData);

                DB::table('user_permission')->insert([
                    'user_id' => 1,
                    'permission_id' => $permissionId,
                ]);

                DB::table('role_permission')->insert([
                    'role_id' => $superAdminRole->id,
                    'permission_id' => $permissionId,
                ]);
            }
        }
    }


    private function addNewPaymentGateways($editor)
    {
        // for paystack payment method
        if (env('PAYSTACK_IS_ACTIVE') === null) {
            $editor->setKey('PAYSTACK_IS_ACTIVE', 0);
        }

        if (env('PAYSTACK_PUBLIC_KEY') === null) {
            $editor->setKey('PAYSTACK_PUBLIC_KEY', '');
        }

        if (env('PAYSTACK_SECRET_KEY') === null) {
            $editor->setKey('PAYSTACK_SECRET_KEY', '');
        }

        if (env('PAYSTACK_PAYMENT_URL') === null) {
            $editor->setKey('PAYSTACK_PAYMENT_URL', 'https://api.paystack.co');
        }

        if (env('MERCHANT_EMAIL') === null) {
            $editor->setKey('MERCHANT_EMAIL', '');
        }

        // for razorpay payment method
        if (env('RAZORPAY_IS_ACTIVE') === null) {
            $editor->setKey('RAZORPAY_IS_ACTIVE', 0);
        }

        if (env('RAZORPAY_KEY_ID') === null) {
            $editor->setKey('RAZORPAY_KEY_ID', '');
        }

        if (env('RAZORPAY_KEY_SECRET') === null) {
            $editor->setKey('RAZORPAY_KEY_SECRET', '');
        }
    }
}
