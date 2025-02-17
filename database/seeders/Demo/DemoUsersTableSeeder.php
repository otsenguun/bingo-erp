<?php

namespace Database\Seeders\Demo;

use Illuminate\Database\Seeder;

class DemoUsersTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('users')->delete();

        \DB::table('users')->insert([
            0 => [
                'id' => 1,
                'name' => tenant()->name,
                'email' => tenant()->email,
                'email_verified_at' => '2022-04-30 22:13:36',
                'password' => tenant()->password,
                'remember_token' => null,
                'account_role' => 1,
                'is_active' => 1,
                'slug' => 'super-admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            1 => [
                'id' => 2,
                'name' => 'Whilemina Watts',
                'email' => 'Whilemina@mailinator.com',
                'email_verified_at' => null,
                'password' => '$2y$10$jn0Si9GEEspQCwBtK1U19e398DDfSw0Iq/UrOobFj1XY9sfn8/R9q',
                'remember_token' => null,
                'account_role' => 0,
                'is_active' => 1,
                'slug' => 'whilemina',
                'created_at' => now(),
                'updated_at' => now(),
            ],
             2=> [
                'id' => 3,
                'name' => 'Sales',
                'email' => 'sales@acculance.top',
                'email_verified_at' => null,
                'password' => '$2y$10$fY.rtjOtoLG1pbxBuGVwmOW/vVXmVKm5mVGvhtIP6Yb48PMmsP/Uq', // acculance2024
                'remember_token' => null,
                'account_role' => 0,
                'is_active' => 1,
                'slug' => 'mari',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            3 => [
                'id' => 4,
                'name' => 'Manager',
                'email' => 'manager@acculance.top',
                'email_verified_at' => null,
                'password' => '$2y$10$fY.rtjOtoLG1pbxBuGVwmOW/vVXmVKm5mVGvhtIP6Yb48PMmsP/Uq', // acculance2024
                'remember_token' => null,
                'account_role' => 0,
                'is_active' => 1,
                'slug' => 'paki',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            4 => [
                'id' => 5,
                'name' => 'Alamin',
                'email' => 'developer@acculance.top',
                'email_verified_at' => null,
                'password' => '$2y$10$fY.rtjOtoLG1pbxBuGVwmOW/vVXmVKm5mVGvhtIP6Yb48PMmsP/Uq', // acculance2024
                'remember_token' => null,
                'account_role' => 1,
                'is_active' => 1,
                'slug' => 'alamin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
