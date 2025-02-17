<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class FreshDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:fresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all tenants database and refresh current database with seed.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Artisan::call('tenants:delete');
        $this->info(Artisan::output());

        $this->info('Migrate fresh central database and tenant database seed. Please wait...');
        Artisan::call('migrate:fresh --seed');
        $this->info(Artisan::output());

        $this->info('Deleted all tenants databases and refreshed central, tenant databases with seed.');

        return Command::SUCCESS;
    }
}
