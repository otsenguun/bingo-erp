<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CentralCurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
          // check if table is empty
          if (DB::table('central_currencies')->count() == 0) {
            DB::table('central_currencies')->insert([
                [
                    'name' => 'United States Dollar',
                    'slug' => 'united-states-dollar',
                    'code' => 'usd',
                    'rate' =>  1,
                    'symbol' => '$',
                    'position' => 'left',
                    'note' => 'This is default currency',
                    'status' => 1,
                ],
                [
                    'name' => 'Bangladeshi Taka',
                    'slug' => 'bangladeshi-taka',
                    'code' => 'BDT',
                    'rate' =>  119.67,
                    'symbol' => '৳',
                    'position' => 'left',
                    'note' => '',
                    'status' => 1,
                ],
                [
                    'name' => 'Indian Rupee',
                    'slug' => 'indian-rupee',
                    'code' => 'INR',
                    'rate' =>  110,
                    'symbol' => '₹',
                    'position' => 'left',
                    'note' => '',
                    'status' => 1,
                ],
                [
                    'name' => 'Nigerian Naira',
                    'slug' => 'nigerian-naira',
                    'code' => 'NGN',
                    'rate' =>  1592.35,
                    'symbol' => '₦',
                    'position' => 'left',
                    'note' => '',
                    'status' => 1,
                ],
            ]);
        }
    }
}
