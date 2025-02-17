<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DeleteTenantDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenants:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete Tenants.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
            Tenant::all()->map(function ($tenant) {
                $tenant->delete();
            });

        $this->info('Tenants deleted Successful!');

        return Command::SUCCESS;
    }
}
