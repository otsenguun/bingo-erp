<?php

namespace App\Console\Commands;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use App\Notifications\DatabaseBackupNotification;

class BackupAndEmailDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:email-database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup the database and send it via email';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $fileName = env('DB_DATABASE') . '_' . Carbon::now()->getTimestamp() . '.sql';
        Artisan::call('database:backup', ['fileName' => $fileName]);
        $pathToFile = storage_path('backup') . DIRECTORY_SEPARATOR . $fileName;

        // Retrieve the super admin and send the notification
        $superAdmin = User::where('account_role', 1)->first();
        if ($superAdmin) {
            $superAdmin->notify(new DatabaseBackupNotification($pathToFile));
            $this->info("Database backup completed and sent to super admin.");
        } else {
            $this->error("No super admin found to notify.");
        }




        // Iterate through each tenant and perform the backup
        // tenancy()->query()->cursor()->each(function ($tenant) {
        //     tenancy()->initialize($tenant);

        //     // Get the tenant-specific database name
        //     $tenantDatabase = env('TENANT_DB_PREFIX') . $tenant->id;
        //     config(['database.connections.tenant.database' => $tenantDatabase]);

        //     // Define backup file name and path
        //     $fileName = $tenantDatabase . '_' . Carbon::now()->getTimestamp() . '.sql';
        //     $tenantBackupDirectory = "{$tenant->id}/backup";
        //     $relativeBackupPath = $tenantBackupDirectory . '/' . $fileName;

        //     // Ensure the backup directory exists
        //     Storage::disk('tenant-public')->makeDirectory($tenantBackupDirectory);

        //     // Get full path for the backup file
        //     $fullBackupPath = Storage::disk('tenant-public')->path($relativeBackupPath);

        //     // Run the database backup command for this tenant
        //     Artisan::call('database:backup', ['fileName' => $fullBackupPath]);

        //     // Retrieve the tenant's admin and send the notification
        //     $admin = User::where('account_role', 'admin')->where('tenant_id', $tenant->id)->first();
        //     if ($admin) {
        //         $admin->notify(new DatabaseBackupNotification($fullBackupPath));
        //         $this->info("Backup completed and sent to admin for tenant {$tenant->id}");
        //     } else {
        //         $this->error("No admin found for tenant {$tenant->id}");
        //     }

        //     // End the tenant-specific connection
        //     tenancy()->end();
        // });
    }
}
