<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Plan::insert([
            [
                'name' => 'New Business',
                'amount' => 300,
                'currency' => 'usd',
                'interval' => 'month',
                'description' => 'Best for small businesses',
                'limit_clients' => 10,
                'limit_suppliers' => 10,
                'limit_employees' => 5,
                'limit_domains' => 2,
                'limit_purchases' => 999,
                'limit_invoices' => 999,
            ], [
                'name' => 'Growing Business',
                'amount' => 400,
                'currency' => 'usd',
                'interval' => 'month',
                'description' => 'Best for medium businesses',
                'limit_clients' => 100,
                'limit_suppliers' => 100,
                'limit_employees' => 10,
                'limit_domains' => 5,
                'limit_purchases' => 9999,
                'limit_invoices' => 9999,
            ], [
                'name' => 'Pro Marketer',
                'amount' => 500,
                'currency' => 'usd',
                'interval' => 'month',
                'description' => 'Best for large businesses',
                'limit_clients' => 0,
                'limit_suppliers' => 0,
                'limit_employees' => 0,
                'limit_domains' => 0,
                'limit_purchases' => 0,
                'limit_invoices' => 0,
            ],
        ]);
    }
}
