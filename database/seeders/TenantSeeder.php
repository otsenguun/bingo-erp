<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    
    public function run()
    {
        $host = env('CENTRAL_DOMAIN');
        foreach(['john', 'jane'] as $domain){
            windowsTestSubHostReg($domain.'.' . $host); 
        }
        
        $tenant1 = Tenant::create([
            'company' => 'Codeshaper',
            'name' => 'John Doe',
            'domain' => 'john',
            'email' => 'john@acculance.top',
            'email_verified_at' => now(),
            'password' => '$2y$10$hdbEMjqcQPr6a4b0/mfWMOEqiG1uaEtWsYmLdf8vtchCrxksFYieK', // acculance2024
            'ready' => false,
            // some other stuff, if you need. like cashier trials
            'trial_ends_at' => now()->addDays(1000),
            'trial_ends_email_sent_at' => null,
            'primary_domain_id' => null,
            'fallback_domain_id' => null,
            'is_banned' => false,
        ]);

        $domain = $tenant1->createDomain([
            'domain' => 'john',
        ]);

        $tenant1->update([
            'ready' => true,
            'primary_domain_id' => $domain->id,
            'fallback_domain_id' => $domain->id,
        ]);

        $tenant2 = Tenant::create([
            'company' => 'Codeshaper',
            'name' => 'Jane Doe',
            'domain' => 'jane',
            'email' => 'jane@acculance.top',
            'email_verified_at' => now(),
            'password' => '$2y$10$hdbEMjqcQPr6a4b0/mfWMOEqiG1uaEtWsYmLdf8vtchCrxksFYieK', // acculance2024
            'ready' => false,
            // some other stuff, if you need. like cashier trials
            'trial_ends_at' => now()->addDays(1000),
            'trial_ends_email_sent_at' => null,
            'primary_domain_id' => null,
            'fallback_domain_id' => null,
            'is_banned' => false,
        ]);

        $domain = $tenant2->createDomain([
            'domain' => 'jane',
        ]);

        $tenant2->update([
            'ready' => true,
            'primary_domain_id' => $domain->id,
            'fallback_domain_id' => $domain->id,
        ]);
    }
}
