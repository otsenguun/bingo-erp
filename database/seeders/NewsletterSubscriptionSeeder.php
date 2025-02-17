<?php

namespace Database\Seeders;

use App\Models\NewsletterSubscription;
use Illuminate\Database\Seeder;

class NewsletterSubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $fakeEmails = [
            "john.doe@example.com",
            "jane.smith@example.com",
            "test.user123@gmail.com",
            "fake.email@fakedomain.com",
            "notreal@example.net",
            "random.email@example.org",
            "user42@example.com",
            "webmaster@example.org",
            "admin@example.net",
            "customer.service@example.com"
        ];

        foreach($fakeEmails as $email){
            NewsletterSubscription::create([
                'email' => $email
            ]);
        }
    }
}
