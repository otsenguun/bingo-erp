<?php

namespace App\Http\Controllers\Central;

use App\Models\Page;
use App\Models\Plan;
use App\Models\Feature;
use App\Models\Payment;
use App\Models\SettingImage;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use App\Models\GeneralSetting;
use App\Models\CentralCurrency;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Intervention\Image\Facades\Image;
use App\Http\Requests\SMSConfigurationRequest;
use App\Http\Requests\MailConfigurationRequest;
use App\Http\Resources\CentralCurrencyResource;
use Jackiedo\DotenvEditor\Facades\DotenvEditor;
use App\Http\Requests\Setting\UpdateHeroSettingsRequest;
use App\Http\Requests\Setting\UpdateWhyUsSettingsRequest;
use App\Http\Requests\Setting\UpdateAboutUsSettingsRequest;
use App\Http\Requests\Setting\UpdateAdvancedSettingsRequest;
use App\Http\Requests\Setting\UpdateFeaturesSettingsRequest;
use App\Http\Requests\Setting\UpdateCustomHtmlSettingsRequest;
use App\Http\Requests\Setting\UpdateGetStartedSettingsRequest;
use App\Http\Requests\Setting\UpdateNewsletterSettingsRequest;
use App\Http\Requests\Setting\UpdateAllFeaturesSettingsRequest;
use App\Http\Requests\Setting\UpdatePricingPlanSettingsRequest;
use App\Http\Requests\Setting\UpdateTestimonialSettingsRequest;
use App\Http\Requests\Setting\UpdateBusinessStartSettingsRequest;
use App\Http\Requests\Setting\UpdateSoftwareOverviewSettingsRequest;

class CentralGeneralController extends Controller
{
    // define middleware
    public function __construct()
    {
        $this->middleware('can:general-settings', ['only' => ['updateGeneralSettings']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getGeneralSettings()
    {
        $editor = DotenvEditor::load();
        $centralDomain = $editor->getKey('CENTRAL_DOMAIN')['value'];

        $query = GeneralSetting::get();
        $plans = Plan::with('features')->get();
        $allFeatures = Feature::all();

        $hasDataInCentralPaymentTable = Payment::count() > 0;

        // remove features that already exist in plans and add to all_features
        foreach ($plans as &$plan) {
            $features = $allFeatures->filter(function ($feature) use ($plan) {
                return !$plan->features->contains($feature->id);
            });
            $plan->all_features = $features;
        }

        $pages = Page::where('status', 1)->get();

        $settings = collect([
            'version' => config('app.version'),
            'centralDomain' => $centralDomain,
            'companyName' => $query->where('key', 'company_name')->first()?->value,
            'companyTagline' => $query->where('key', 'company_tagline')->first()?->value,
            'email' => $query->where('key', 'email_address')->first()?->value,
            'phone' => $query->where('key', 'phone_number')->first()?->value,
            'address' => $query->where('key', 'address')->first()?->value,
            'language' => $query->where('key', 'default_language')->first()?->value,
            'logo' => global_asset('images/' . $query->where('key', 'logo')->first()?->value),
            'blackLogo' => global_asset('images/' . $query->where('key', 'logo_black')->first()?->value),
            'smallLogo' => global_asset('images/' . $query->where('key', 'small_logo')->first()?->value),
            'favicon' => global_asset('images/' . $query->where('key', 'favicon')->first()?->value),
            'copyright' => $query->where('key', 'copyright')->first()?->value,
            'hasDataInCentralPaymentTable' => $hasDataInCentralPaymentTable,

            'currency' => new CentralCurrencyResource(CentralCurrency::where(
                'id',
                (int) $query->where('key', 'default_currency')->first()->value
            )->first()),

            'facebook_link' => $query->where('key', 'facebook_link')->first()?->value,
            'instagram_link' => $query->where('key', 'instagram_link')->first()?->value,
            'twitter_link' => $query->where('key', 'twitter_link')->first()?->value,
            'linkedin_link' => $query->where('key', 'linkedin_link')->first()?->value,
            'trial_day_count' => $query->where('key', 'trial_day_count')->first()?->value ?? 14,

            'why_us_cards' => SettingImage::where('status', 1)->where('type', 'why_us_cards')->get(),
            'features' => SettingImage::where('status', 1)->where('type', 'features')->get(),
            'explorers' => SettingImage::where('status', 1)->where('type', 'explorers')->get(),
            'all_features' => SettingImage::where('status', 1)->where('type', 'all_features')->get(),
            'software_overview_images' => SettingImage::where('status', 1)->where(
                'type',
                'software_overview_images'
            )->get(),
            'testimonials' => SettingImage::where('status', 1)->where('type', 'testimonials')->get(),
            'brands' => SettingImage::where('status', 1)->where('type', 'brands')->get(),
            'plans' => $plans,
            'plan_discount' => number_format((float)$query->where('key', 'plan_discount')->first()?->value),

            'is_show_hero_section' => $query->where('key', 'is_show_hero_section')->first()?->value === 'yes',
            'is_show_about_us_section' => $query->where('key', 'is_show_about_us_section')->first()?->value === 'yes',
            'is_show_why_us_section' => $query->where('key', 'is_show_why_us_section')->first()?->value === 'yes',
            'is_show_business_start_section' => $query->where('key', 'is_show_business_start_section')->first()?->value === 'yes',
            'is_show_features_section' => $query->where('key', 'is_show_features_section')->first()?->value === 'yes',
            'is_show_business_need_section' => $query->where('key', 'is_show_business_need_section')->first()?->value === 'yes',
            'is_show_extra_features_section' => $query->where('key', 'is_show_extra_features_section')->first()?->value === 'yes',
            'is_show_cta_section' => $query->where('key', 'is_show_cta_section')->first()?->value === 'yes',
            'is_show_screenshot_section' => $query->where('key', 'is_show_screenshot_section')->first()?->value === 'yes',
            'is_show_pricing_section' => $query->where('key', 'is_show_pricing_section')->first()?->value === 'yes',
            'is_show_testimonial_section' => $query->where('key', 'is_show_testimonial_section')->first()?->value === 'yes',
            'is_show_brand_section' => $query->where('key', 'is_show_brand_section')->first()?->value === 'yes',
            'is_show_newsletter_section' => $query->where('key', 'is_show_newsletter_section')->first()?->value === 'yes',

            'information_plans' => $pages->where('type', Page::TYPE_INFORMATION)->values(),
            'need_help_plans' => $pages->where('type', Page::TYPE_NEED_HELP)->values(),
        ]);

        $settings = $settings->merge(settings()->all());

        return response()->json($settings);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateGeneralSettings(Request $request)
    {
        $validator = $request->validate([
            'companyName' => 'required|string|max:30',
            'companyTagline' => 'required|string|max:255|min:3',
            'emailAddress' => 'required|string|email|max:80',
            'phoneNumber' => 'nullable|string|max:255',
            'address' => 'required|string|max:255',
            'language' => 'required|string|min:2|max:10',
            'currency' => 'required',
            'copyrightText' => 'required|string|max:100',

            'facebook_link' => ['nullable', 'url', 'max:255'],
            'instagram_link' => ['nullable', 'url', 'max:255'],
            'twitter_link' => ['nullable', 'url', 'max:255'],
            'linkedin_link' => ['nullable', 'url', 'max:255'],
            'trial_day_count' => ['min:0', 'integer'],
            'plan_discount' => ['nullable', 'min:1', 'max:99', 'integer'],
        ]);

        // get settings data
        $allSettings = GeneralSetting::get();

        // upload logo
        $logoName = $allSettings->where('key', 'logo')->first()->value;
        if ($request->logo) {
            $logoName = handleGeneralSettingsImage($request->logo, $logoName, 'logo');
        }

        // upload black logo
        $blackLogoName = $allSettings->where('key', 'logo_black')->first()->value;
        if ($request->blackLogo) {
            $blackLogoName = handleGeneralSettingsImage($request->blackLogo, $blackLogoName, 'logo-black');
        }

        // upload small logo
        $smallLogoName = $allSettings->where('key', 'small_logo')->first()->value;
        if ($request->smallLogo) {
            $smallLogoName = handleGeneralSettingsImage($request->smallLogo, $smallLogoName, 'small-logo');
        }

        // upload favicon
        $favicon = $allSettings->where('key', 'favicon')->first()->value;
        if ($request->favicon) {
            $favicon = handleGeneralSettingsImage($request->favicon, $favicon, 'favicon');
        }

        // update general settings
        $allSettings->where('key', 'company_name')->first()->update(['value' => clean($request->companyName)]);
        $allSettings->where('key', 'company_tagline')->first()->update(['value' => clean($request->companyTagline)]);
        $allSettings->where('key', 'email_address')->first()->update(['value' => $request->emailAddress]);
        $allSettings->where('key', 'phone_number')->first()->update(['value' => $request->phoneNumber]);
        $allSettings->where('key', 'address')->first()->update(['value' => clean($request->address)]);
        $allSettings->where('key', 'default_currency')->first()->update(['value' => clean($request->currency['id'])]);
        $allSettings->where('key', 'default_language')->first()->update(['value' => clean($request->language)]);
        $allSettings->where('key', 'logo')->first()->update(['value' => $logoName]);
        $allSettings->where('key', 'logo_black')->first()->update(['value' => $blackLogoName]);
        $allSettings->where('key', 'small_logo')->first()->update(['value' => $smallLogoName]);
        $allSettings->where('key', 'favicon')->first()->update(['value' => $favicon]);
        $allSettings->where('key', 'copyright')->first()->update(['value' => clean($request->copyrightText)]);

        $allSettings->where('key', 'facebook_link')->first()->update(['value' => $request->facebook_link]);
        $allSettings->where('key', 'instagram_link')->first()->update(['value' => $request->instagram_link]);
        $allSettings->where('key', 'twitter_link')->first()->update(['value' => $request->twitter_link]);
        $allSettings->where('key', 'linkedin_link')->first()->update(['value' => $request->linkedin_link]);
        $allSettings->where('key', 'trial_day_count')->first()->update(['value' => $request->trial_day_count]);
        $allSettings->where('key', 'plan_discount')->first()->update(['value' => $request->plan_discount]);

        // add activity log
        activity()
        ->causedBy(Auth::user())
        // ->performedOn($allSettings)
        ->withProperties([
            'name' => '',
            'code' => '',
            'event' => 'Update',
            'slug' => '/',
            'routeName' => 'setup.general'
        ])
        ->useLog('General Settings Updated')
        ->log('General Settings Updated');

        return $this->responseWithSuccess('Settings updated successfully!');
    }

    public function updateShowHideSection(Request $request){
        $settingValue = $request['value'] ? 'yes' : 'no';
        $setting = GeneralSetting::where('key', $request['key'])->first();
        if ($setting) {
            $setting->update(['value' => $settingValue]);
            return response()->json(['message' => 'Toggle updated successfully.'], 200);
        }

        return response()->json(['message' => 'Setting not found.'], 404);
    }

    public function getHeroSettings()
    {
        $data['hero_tagline'] = settings()->get('hero_tagline');
        $data['hero_title'] = settings()->get('hero_title');
        $data['hero_description'] = settings()->get('hero_description');
        $data['hero_demo_button_text'] = settings()->get('hero_demo_button_text');
        $data['hero_demo_button_link'] = settings()->get('hero_demo_button_link');
        $data['hero_get_started_button_text'] = settings()->get('hero_get_started_button_text');
        $data['hero_get_started_button_link'] = settings()->get('hero_get_started_button_link');

        return $this->responseWithSuccess('Settings retrieved successfully!', $data);
    }

    public function updateHeroSettings(UpdateHeroSettingsRequest $request)
    {
        settings()->set($request->validated());
        settings()->save();

        $allSettings = GeneralSetting::get();
        $allSettings->where('key', 'is_show_hero_section')->first()->update(['value' => ($request->is_show_hero_section === true ? 'yes' : 'no')]);

        // add activity log
        activity()
        ->causedBy(Auth::user())
        ->withProperties([
            'name' => '',
            'code' =>  '',
            'event' => 'Update',
            'slug' => '/',
            'routeName' => 'settings.hero'
        ])
        ->useLog('Landing Page Hero Section Updated')
        ->log('Landing Page Hero Section Updated');

        return $this->responseWithSuccess('Settings updated successfully!');
    }

    public function getAboutUsSettings()
    {
        $data['about_us_tagline'] = settings()->get('about_us_tagline');
        $data['about_us_title'] = settings()->get('about_us_title');
        $data['about_us_description'] = settings()->get('about_us_description');

        return $this->responseWithSuccess('Settings retrieved successfully!', $data);
    }

    public function updateAboutUsSettings(UpdateAboutUsSettingsRequest $request)
    {
        settings()->set($request->validated());
        settings()->save();

        $allSettings = GeneralSetting::get();
        $allSettings->where('key', 'is_show_about_us_section')->first()->update(['value' => ($request->is_show_about_us_section === true ? 'yes' : 'no')]);

        // add activity log
        activity()
        ->causedBy(Auth::user())
        ->withProperties([
            'name' => '',
            'code' =>  '',
            'event' => 'Update',
            'slug' => '/',
            'routeName' => 'settings.about-us'
        ])
        ->useLog('Landing Page About Us Section Updated')
        ->log('Landing Page About Us Section Updated');

        return $this->responseWithSuccess('Settings updated successfully!');
    }

    public function getWhyUsSettings()
    {
        $data['why_us_tagline'] = settings()->get('why_us_tagline');
        $data['why_us_title'] = settings()->get('why_us_title');
        $data['why_us_description'] = settings()->get('why_us_description');

        return $this->responseWithSuccess('Settings retrieved successfully!', $data);
    }

    public function updateWhyUsSettings(UpdateWhyUsSettingsRequest $request)
    {
        settings()->set($request->validated());
        settings()->save();

        $allSettings = GeneralSetting::get();
        $allSettings->where('key', 'is_show_why_us_section')->first()->update(['value' => ($request->is_show_why_us_section === true ? 'yes' : 'no')]);

        // add activity log
        activity()
        ->causedBy(Auth::user())
        ->withProperties([
            'name' => '',
            'code' =>  '',
            'event' => 'Update',
            'slug' => '/',
            'routeName' => 'settings.why-us'
        ])
        ->useLog('Landing Page Why Us Section Updated')
        ->log('Landing Page Why Us Section Updated');

        return $this->responseWithSuccess('Settings updated successfully!');
    }

    public function getBusinessStartSettings()
    {
        $data['business_start_section_tagline'] = settings()->get('business_start_section_tagline');
        $data['business_start_section_title'] = settings()->get('business_start_section_title');
        $data['business_start_section_description'] = settings()->get('business_start_section_description');
        $data['business_start_support_list'] = json_decode(settings()->get('business_start_support_list', '[]'));

        return $this->responseWithSuccess('Settings retrieved successfully!', $data);
    }

    public function updateBusinessStartSettings(UpdateBusinessStartSettingsRequest $request)
    {
        settings()->set(
            $request->safe()->except('business_start_support_list') + [
                'business_start_support_list' => json_encode($request->business_start_support_list),
            ]
        );
        settings()->save();

        $allSettings = GeneralSetting::get();
        $allSettings->where('key', 'is_show_business_start_section')->first()->update(['value' => ($request->is_show_business_start_section === true ? 'yes' : 'no')]);

        // add activity log
        activity()
        ->causedBy(Auth::user())
        ->withProperties([
            'name' => '',
            'code' =>  '',
            'event' => 'Update',
            'slug' => '/',
            'routeName' => 'settings.business-start'
        ])
        ->useLog('Landing Page Business Start Section Updated')
        ->log('Landing Page Business Start Section Updated');

        return $this->responseWithSuccess('Settings updated successfully!');
    }

    public function getFeaturesSettings()
    {
        $data['features_section_tagline'] = settings()->get('features_section_tagline');
        $data['features_section_title'] = settings()->get('features_section_title');
        $data['features_section_description'] = settings()->get('features_section_description');

        return $this->responseWithSuccess('Settings retrieved successfully!', $data);
    }

    public function updateFeaturesSettings(UpdateFeaturesSettingsRequest $request)
    {
        settings()->set($request->validated());
        settings()->save();

        $allSettings = GeneralSetting::get();
        $allSettings->where('key', 'is_show_features_section')->first()->update(['value' => ($request->is_show_features_section === true ? 'yes' : 'no')]);

        // add activity log
        activity()
        ->causedBy(Auth::user())
        ->withProperties([
            'name' => '',
            'code' =>  '',
            'event' => 'Update',
            'slug' => '/',
            'routeName' => 'settings.features'
        ])
        ->useLog('Landing Page Features Section Updated')
        ->log('Landing Page Features Section Updated');

        return $this->responseWithSuccess('Settings updated successfully!');
    }

    public function getAllFeaturesSettings()
    {
        $data['all_features_section_tagline'] = settings()->get('all_features_section_tagline');
        $data['all_features_section_title'] = settings()->get('all_features_section_title');

        return $this->responseWithSuccess('Settings retrieved successfully!', $data);
    }

    public function getGetStartedSettings()
    {
        $data['get_started_box_title'] = settings()->get('get_started_box_title');
        $data['get_started_box_description'] = settings()->get('get_started_box_description');
        $data['get_started_box_button_text'] = settings()->get('get_started_box_button_text');
        $data['get_started_box_button_link'] = settings()->get('get_started_box_button_link');

        return $this->responseWithSuccess('Settings retrieved successfully!', $data);
    }

    public function updateAllFeaturesSettings(UpdateAllFeaturesSettingsRequest $request)
    {
        settings()->set($request->validated());
        settings()->save();

        $allSettings = GeneralSetting::get();
        $allSettings->where('key', 'is_show_extra_features_section')->first()->update(['value' => ($request->is_show_extra_features_section === true ? 'yes' : 'no')]);

        // add activity log
        activity()
        ->causedBy(Auth::user())
        ->withProperties([
            'name' => '',
            'code' =>  '',
            'event' => 'Update',
            'slug' => '/',
            'routeName' => 'settings.all-features'
        ])
        ->useLog('Landing Page All features Section Updated')
        ->log('Landing Page All features Section Updated');

        return $this->responseWithSuccess('Settings updated successfully!');
    }

    public function updateGetStartedSettings(UpdateGetStartedSettingsRequest $request)
    {
        settings()->set($request->validated());
        settings()->save();

        $allSettings = GeneralSetting::get();
        $allSettings->where('key', 'is_show_cta_section')->first()->update(['value' => ($request->is_show_cta_section === true ? 'yes' : 'no')]);

        // add activity log
        activity()
        ->causedBy(Auth::user())
        ->withProperties([
            'name' => '',
            'code' =>  '',
            'event' => 'Update',
            'slug' => '/',
            'routeName' => 'settings.cta'
        ])
        ->useLog('Landing Page Get Started Section Updated')
        ->log('Landing Page Get Started Section Updated');

        return $this->responseWithSuccess('Settings updated successfully!');
    }

    public function getSoftwareOverviewSettings()
    {
        $data['software_overview_section_tagline'] = settings()->get('software_overview_section_tagline');
        $data['software_overview_section_title'] = settings()->get('software_overview_section_title');

        return $this->responseWithSuccess('Settings retrieved successfully!', $data);
    }

    public function updateSoftwareOverviewSettings(UpdateSoftwareOverviewSettingsRequest $request)
    {
        settings()->set($request->validated());
        settings()->save();

        $allSettings = GeneralSetting::get();
        $allSettings->where('key', 'is_show_screenshot_section')->first()->update(['value' => ($request->is_show_screenshot_section === true ? 'yes' : 'no')]);

        // add activity log
        activity()
        ->causedBy(Auth::user())
        ->withProperties([
            'name' => '',
            'code' =>  '',
            'event' => 'Update',
            'slug' => '/',
            'routeName' => 'settings.software-overview'
        ])
        ->useLog('Landing Page Software Overview Section Updated')
        ->log('Landing Page Software Overview Section Updated');

        return $this->responseWithSuccess('Settings updated successfully!');
    }

    public function getPricingPlanSettings()
    {
        $data['pricing_plan_section_tagline'] = settings()->get('pricing_plan_section_tagline');
        $data['pricing_plan_section_title'] = settings()->get('pricing_plan_section_title');

        return $this->responseWithSuccess('Settings retrieved successfully!', $data);
    }

    public function updatePricingPlanSettings(UpdatePricingPlanSettingsRequest $request)
    {
        settings()->set($request->validated());
        settings()->save();

        $allSettings = GeneralSetting::get();
        $allSettings->where('key', 'is_show_pricing_section')->first()->update(['value' => ($request->is_show_pricing_section === true ? 'yes' : 'no')]);

        // add activity log
        activity()
        ->causedBy(Auth::user())
        ->withProperties([
            'name' => '',
            'code' =>  '',
            'event' => 'Update',
            'slug' => '/',
            'routeName' => 'settings.pricing-plan'
        ])
        ->useLog('Landing Page Pricing Plan Section Updated')
        ->log('Landing Page Pricing Plan Section Updated');

        return $this->responseWithSuccess('Settings updated successfully!');
    }

    public function getNewsletterSettings()
    {
        $data['newsletter_section_title'] = settings()->get('newsletter_section_title');
        $data['newsletter_section_description'] = settings()->get('newsletter_section_description');

        return $this->responseWithSuccess('Settings retrieved successfully!', $data);
    }

    public function updateNewsletterSettings(UpdateNewsletterSettingsRequest $request)
    {
        settings()->set($request->validated());
        settings()->save();

        $allSettings = GeneralSetting::get();
        $allSettings->where('key', 'is_show_newsletter_section')->first()->update(['value' => ($request->is_show_newsletter_section === true ? 'yes' : 'no')]);

        // add activity log
        activity()
        ->causedBy(Auth::user())
        ->withProperties([
            'name' =>'',
            'code' =>  '',
            'event' => 'Update',
            'slug' => '/',
            'routeName' => 'settings.newsletter'
        ])
        ->useLog('Landing Page Newsletter Section Section Updated')
        ->log('Landing Page Newsletter Section Section Updated');

        return $this->responseWithSuccess('Settings updated successfully!');
    }

    public function getTestimonialSettings()
    {
        $data['testimonial_section_tagline'] = settings()->get('testimonial_section_tagline');
        $data['testimonial_section_title'] = settings()->get('testimonial_section_title');
        return $this->responseWithSuccess('Settings retrieved successfully!', $data);
    }

    public function updateTestimonialSettings(UpdateTestimonialSettingsRequest $request)
    {
        settings()->set($request->validated());
        settings()->save();

        $allSettings = GeneralSetting::get();
        $allSettings->where('key', 'is_show_testimonial_section')->first()->update(['value' => ($request->is_show_testimonial_section === true ? 'yes' : 'no')]);

        // add activity log
        activity()
        ->causedBy(Auth::user())
        ->withProperties([
            'name' => '',
            'code' =>  '',
            'event' => 'Update',
            'slug' => '/',
            'routeName' => 'settings.testimonial'
        ])
        ->useLog('Landing Page Testimonial Section Updated')
        ->log('Landing Page Testimonial Section Updated');

        return $this->responseWithSuccess('Settings updated successfully!');
    }

    public function getAdvancedSettings()
    {
        $editor = DotenvEditor::load();
        $data['stripe_public_key'] = $editor->getKey('STRIPE_KEY');
        $data['stripe_private_key'] = $editor->getKey('STRIPE_SECRET');
        return $this->responseWithSuccess('Advanced settings retrieved successfully!', $data);
    }

    /**
     * @throws \Brotzka\DotenvEditor\Exceptions\DotEnvException
     */
    public function updateAdvancedSettings(UpdateAdvancedSettingsRequest $request)
    {
        $editor = DotenvEditor::load();
        $editor->setKey('STRIPE_KEY', $request->stripe_public_key);
        $editor->setKey('STRIPE_SECRET', $request->stripe_private_key);
        $editor->save();
        return $this->responseWithSuccess('Advanced settings updated successfully!');
    }

    public function getCustomHtmlSettings()
    {
        $data['custom_html'] = settings()->get('custom_html');
        return $this->responseWithSuccess('Settings retrieved successfully!', $data);
    }

    public function updateCustomHtmlSettings(UpdateCustomHtmlSettingsRequest $request)
    {
        settings()->set($request->validated());
        settings()->save();

        // add activity log
        activity()
        ->causedBy(Auth::user())
        ->withProperties([
            'name' => '',
            'code' =>  '',
            'event' => 'Update',
            'slug' => '/',
            'routeName' => 'settings.custom-html'
        ])
        ->useLog('Custom HTML Updated')
        ->log('Custom HTML Updated');

        return $this->responseWithSuccess('Settings updated successfully!');
    }

    public function getMailConfiguration()
    {
        $editor = DotenvEditor::load();
        $data = [
            'mail_mailer' => $editor->getKey('MAIL_MAILER'),
            'mail_host' => $editor->getKey('MAIL_HOST'),
            'mail_port' =>  $editor->getKey('MAIL_PORT'),
            'mail_username' => $editor->getKey('MAIL_USERNAME'),
            'mail_password' => $editor->getKey('MAIL_PASSWORD'),
            'mail_encryption' => $editor->getKey('MAIL_ENCRYPTION'),
            'mail_from_address' => $editor->getKey('MAIL_FROM_ADDRESS'),
            'mail_from_name' => $editor->getKey('MAIL_FROM_NAME')
        ];
        return $data;
    }

    public function getSMSConfiguration()
    {
        $editor = DotenvEditor::load();

        $data = [
            'twilio_account_sid' => $editor->getKey('TWILIO_ACCOUNT_SID'),
            'twilio_auth_token' => $editor->getKey('TWILIO_AUTH_TOKEN'),
            'twilio_from' =>  $editor->getKey('TWILIO_FROM'),
            'twilio_sms_service_sid' => $editor->getKey('TWILIO_SMS_SERVICE_SID')
        ];
        return $data;
    }

    public function updateMailConfiguration(MailConfigurationRequest $request)
    {
        $editor = DotenvEditor::load();
        $editor->setKey('MAIL_MAILER', $request->mail_mailer);
        $editor->setKey('MAIL_HOST', $request->mail_host);
        $editor->setKey('MAIL_PORT', $request->mail_port);
        $editor->setKey('MAIL_USERNAME', $request->mail_username);
        $editor->setKey('MAIL_PASSWORD', $request->mail_password);
        $editor->setKey('MAIL_ENCRYPTION', $request->mail_encryption);
        $editor->setKey('MAIL_FROM_ADDRESS', $request->mail_from_address);
        $editor->setKey('MAIL_FROM_NAME', $request->mail_from_name);
        $editor->save();

        // add activity log
        activity()
        ->causedBy(Auth::user())
        ->withProperties([
            'name' => '',
            'code' => '',
            'event' => 'Update',
            'slug' => '/',
            'routeName' => 'setup.mailConfiguration'
        ])
        ->useLog('Mail Server Credentials Cpdated')
        ->log('Mail Server Credentials Updated');

        return 'Env file updated successfully!';
    }

    public function sendTestConnectionEmail()
    {
        $recipientEmail = GeneralSetting::where('key', 'email_address')->first()->value;
        try {
            Mail::send([], [], function (Message $message) use ($recipientEmail) {
                $message->to($recipientEmail)
                    ->subject('Test Email')
                    ->html('<p>This is a test email to check email credentials.</p>');
            });

            return response()->json(['message' => 'Test email sent successfully!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error sending test email: ' . $e->getMessage()], 500);
        }
    }


    public function updateSMSConfiguration(SMSConfigurationRequest $request)
    {
        $editor = DotenvEditor::load();
        $editor->setKey('TWILIO_ACCOUNT_SID', $request->twilio_account_sid);
        $editor->setKey('TWILIO_AUTH_TOKEN', $request->twilio_auth_token);
        $editor->setKey('TWILIO_FROM', $request->twilio_from);
        $editor->setKey('TWILIO_SMS_SERVICE_SID', $request->twilio_sms_service_sid);
        $editor->save();

        // add activity log
        activity()
        ->causedBy(Auth::user())
        // ->performedOn($allSettings)
        ->withProperties([
            'name' => '',
            'code' => '',
            'event' => 'Update',
            'slug' => '/',
            'routeName' => 'setup.smsConfiguration'
        ])
        ->useLog('SMS Server Credentials Updated')
        ->log('SMS Server Credentials Updated');

        return 'Env file updated successfully!';
    }
}