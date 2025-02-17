<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CentralSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // seed basic data to table
        $settingElements = [
            'company_name' => 'Your Company Name',
            'company_tagline' => 'Your Business Slogan',
            'email_address' => 'email@gmail.com',
            'phone_number' => '0170000000',
            'address' => 'Your address',
            'default_currency' => '1',
            'default_language' => 'en',
            'logo' => 'white_logo.png',
            'logo_black' => 'black_logo.png',
            'small_logo' => 'small_logo.png',
            'favicon' => 'favicon.png',
            'copyright' => 'Copyright 2022 © By Codeshaper. All Rights Reserved.',
            'facebook_link' => 'https://www.facebook.com/codeshaper',
            'instagram_link' => 'https://www.instagram.com/codeshaper',
            'twitter_link' => 'https://www.twitter.com/codeshaper',
            'linkedin_link' => 'https://www.linkedin.com/codeshaper',
            'trial_day_count' => 14,
            'plan_discount' => 0,
            'is_show_hero_section' => 'yes',
            'is_show_about_us_section' => 'yes',
            'is_show_why_us_section' => 'yes',
            'is_show_business_start_section' => 'yes',
            'is_show_features_section' => 'yes',
            'is_show_business_need_section' => 'yes',
            'is_show_extra_features_section' => 'yes',
            'is_show_cta_section' => 'yes',
            'is_show_screenshot_section' => 'yes',
            'is_show_pricing_section' => 'yes',
            'is_show_testimonial_section' => 'yes',
            'is_show_brand_section' => 'yes',
            'is_show_newsletter_section' => 'yes',
            'is_show_template_footer_section' => 'yes',
        ];

        foreach ($settingElements as $key => $value) {
            // Check if the key already exists in the table
            $existingSetting = DB::table('general_settings')->where('key', $key)->first();

            if (!$existingSetting) {
                // Only insert if the key does not already exist
                DB::table('general_settings')->insert([
                    'key' => $key,
                    'display_name' => ucwords(str_replace('_', ' ', $key)),
                    'value' => $value,
                ]);
            }
        }

        if (DB::table('settings')->count() == 0) {
            settings()->set([
                // new setting for landing page
                // hero section
                'hero_tagline' => 'Our Platform, Your Business',
                'hero_title' => 'Acculance SaaS',
                'hero_description' => 'Acculance SaaS is a multitenancy-based SaaS application that enables tenants to manage their expenses, purchases, sales, payments, accounting, inventory, and many more.',
                'hero_demo_button_text' => 'Try Demo',
                'hero_demo_button_link' => '/admin/login',
                'hero_get_started_button_text' => 'Get Started',
                'hero_get_started_button_link' => '/register',

                // about us section
                'about_us_tagline' => 'About Acculance SaaS',
                'about_us_title' => 'Ultimate Sales, Inventory, Accounting Management SaaS Application',
                'about_us_description' => 'Acculance SaaS is an all in one management system that helps business owners to manage and operate their businesses. Acculance SaaS is a multitenancy-based subscription system that allows tenants to register for a subscription plan and get access to lots of features that includes POS, expenses, purchases, sales, payments, accounting, inventory, and many more.',

                // why us section
                'why_us_tagline' => 'Why Acculance SaaS?',
                'why_us_title' => 'Manage All Your Businesses in one place',
                'why_us_description' => 'Acculance SaaS is one of the best Sales, Inventory, and Accounting Management software available in the market. Acculance SaaS is specially built to grow small businesses by adding digitalization to their business.',

                // business start section
                'business_start_section_tagline' => 'Give It a Try',
                'business_start_section_title' => 'Move Your Business & Grow With Us',
                'business_start_section_description' => 'We understand that ideal software can assist you to grow your business on a larger scale. That\'s why Acculance SaaS can be a perfect solution for you. If you are already using any software then you can easily move to AcculanceSaaS. So don\'t hesitate to give it a try today!',
                'business_start_support_list' => json_encode([
                    '14 Days Free Support',
                    '24 Hours Support',
                ]),

                // features section
                'features_section_tagline' => 'Awesome Features',
                'features_section_title' => 'Discover Our Awesome Features',
                'features_section_description' => 'Acculance is an all-in-one management system manage expenses, purchases, sales, payments, accounting, loans, assets, payroll, and many more..',

                // all feature section
                'all_features_section_tagline' => 'Core Modules',
                'all_features_section_title' => 'Core Modules For Your Business',

                // get started box
                'get_started_box_title' => 'Managing Business Has Never Been So Easy.',
                'get_started_box_description' => 'Acculance is an all-in-one management system manage expenses, purchases, sales, payments, accounting.',
                'get_started_box_button_text' => 'Get Started',
                'get_started_box_button_link' => '/register',

                // software overview section
                'software_overview_section_tagline' => 'Dashboard Screenshot',
                'software_overview_section_title' => 'Software Overview',

                // pricing plan section
                'pricing_plan_section_tagline' => 'Price Tags',
                'pricing_plan_section_title' => 'Pricing Plan',

                // testimonial section
                'testimonial_section_tagline' => 'Testimonials',
                'testimonial_section_title' => 'What Our Clients Say',

                // newsletter section
                'newsletter_section_title' => 'Try Acculance SaaS',
                'newsletter_section_description' => 'Access all Acculance SaaS for 14 days, then decide which plan best suits your business.',

                // custom html section
                'custom_html' => <<<HTML
                <!-- Primary Meta Tags -->
                <meta name="title" content="Acculance SaaS - Ultimate Sales, Inventory, Accounting Management SaaS Application.">
                <meta name="description" content="Acculance SaaS is an all in one management system that helps business owners to manage and operate their businesses. Acculance SaaS is a multitenancy-based subscription system that allows tenants to register for a subscription plan and get access to lots of features that includes POS, expenses, purchases, sales, payments, accounting, inventory, and many more. Acculance SaaS is built with core Laravel, Vue JS, Boostrap, and Other modern technologies.">
    
                <!-- Open Graph / Facebook -->
                <meta property="og:type" content="website">
                <meta property="og:url" content="https://acculance.top/">
                <meta property="og:title" content="Acculance SaaS - Ultimate Sales, Inventory, Accounting Management SaaS Application.">
                <meta property="og:description" content="Acculance SaaS is an all in one management system that helps business owners to manage and operate their businesses. Acculance SaaS is a multitenancy-based subscription system that allows tenants to register for a subscription plan and get access to lots of features that includes POS, expenses, purchases, sales, payments, accounting, inventory, and many more. Acculance SaaS is built with core Laravel, Vue JS, Boostrap, and Other modern technologies.">
                <meta property="og:image" content="https://i.ibb.co/fDC0L8h/2-1.png">
    
                <!-- Twitter -->
                <meta name="twitter:card" content="summary">
                <meta name="twitter:card" content="summary_large_image">
                <meta property="twitter:url" content="https://acculance.top/">
                <meta property="twitter:title" content="Acculance SaaS - Ultimate Sales, Inventory, Accounting Management SaaS Application.">
                <meta property="twitter:description" content="Acculance SaaS is an all in one management system that helps business owners to manage and operate their businesses. Acculance SaaS is a multitenancy-based subscription system that allows tenants to register for a subscription plan and get access to lots of features that includes POS, expenses, purchases, sales, payments, accounting, inventory, and many more. Acculance SaaS is built with core Laravel, Vue JS, Boostrap, and Other modern technologies.">
                <meta property="twitter:image" content="https://i.ibb.co/fDC0L8h/2-1.png">
                HTML,
            ]);

            settings()->save();
        }
    }
}
