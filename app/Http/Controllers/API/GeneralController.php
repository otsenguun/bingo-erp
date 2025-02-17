<?php

namespace App\Http\Controllers\API;

use App\Models\Currency;
use Illuminate\Mail\Message;
use App\Models\GeneralSetting;
use App\Services\ImageService;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\CurrencyResource;
use App\Http\Controllers\PaymentController;
use App\Http\Requests\SMSConfigurationRequest;
use App\Http\Requests\MailConfigurationRequest;
use App\Http\Requests\GeneralSetting\StoreGeneralSettingRequest;


class GeneralController extends Controller
{
    protected $imageService;
    // define middleware
    public function __construct(ImageService $imageService)
    {
        $this->middleware('can:general-settings', ['only' => ['updateGeneralSettings']]);

        $this->imageService = $imageService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return array
     */
    public function getGeneralSettings()
    {
        $paymentController = new PaymentController();
        $centralActiveCurrency = $paymentController->centralActiveCurrency();

        
        $query = GeneralSetting::get();

        $settings = [
            'version' => config('app.version'),
            'companyName' => $query->where('key', 'company_name')->first()->value,
            'companyTagline' => $query->where('key', 'company_tagline')->first()->value,
            'email' => $query->where('key', 'email_address')->first()->value,
            'phone' => $query->where('key', 'phone_number')->first()->value,
            'address' => $query->where('key', 'address')->first()->value,
            'clientPrefix' => $query->where('key', 'client_prefix')->first()->value,
            'supplierPrefix' => $query->where('key', 'supplier_prefix')->first()->value,
            'employeePrefix' => $query->where('key', 'employee_prefix')->first()->value,
            'proCatPrefix' => $query->where('key', 'product_cat_prefix')->first()->value,
            'proSubCatPrefix' => $query->where('key', 'product_sub_cat_prefix')->first()->value,
            'productPrefix' => $query->where('key', 'product_prefix')->first()->value,
            'expCatPrefix' => $query->where('key', 'exp_cat_prefix')->first()->value,
            'expSubCatPrefix' => $query->where('key', 'exp_sub_cat_prefix')->first()->value,
            'purchasePrefix' => $query->where('key', 'pur_prefix')->first()->value,
            'purchaseReturnPrefix' => $query->where('key', 'pur_return_prefix')->first()->value,
            'quotationPrefix' => $query->where('key', 'quotation_prefix')->first()->value,
            'invoicePrefix' => $query->where('key', 'invoice_prefix')->first()->value,
            'invoiceReturnPrefix' => $query->where('key', 'invoice_return_prefix')->first()->value,
            'adjustmentPrefix' => $query->where('key', 'adjustment_prefix')->first()->value,
            'currency' => new CurrencyResource(Currency::where('id', (int) $query->where('key', 'default_currency')->first()->value)->first()),
            'centralAdminCurrency' => $centralActiveCurrency,
            'language' => $query->where('key', 'default_language')->first()->value,

            'logo' => global_asset('images/' . $query->where('key', 'logo')->first()?->value),
            'blackLogo' => global_asset('images/' . $query->where('key', 'logo_black')->first()?->value),
            'smallLogo' => global_asset('images/' . $query->where('key', 'small_logo')->first()?->value),
            'favicon' => global_asset('images/' . $query->where('key', 'favicon')->first()?->value),
            'copyright' => $query->where('key', 'copyright')->first()->value,
            'invoiceThankYouMessage' => $query->where('key', 'invoice_thank_you_message')->first()->value ?? '',
            'taxRegistrationNumber' => $query->where('key', 'tax_registration_number')->first()->value ?? '',
            'defaultClientSlug' => $query->where('key', 'default_client_slug')->first()->value,
            'defaultAccountSlug' => $query->where('key', 'default_account_slug')->first()->value,
            'defaultVatRateSlug' => $query->where('key', 'default_vat_rate_slug')->first()->value,
        ];

        return $settings;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function updateGeneralSettings(StoreGeneralSettingRequest $request)
    {
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
        $allSettings->where('key', 'client_prefix')->first()->update(['value' => clean($request->clientPrefix)]);
        $allSettings->where('key', 'supplier_prefix')->first()->update(['value' => clean($request->supplierPrefix)]);
        $allSettings->where('key', 'employee_prefix')->first()->update(['value' => clean($request->employeePrefix)]);
        $allSettings->where('key', 'product_cat_prefix')->first()->update(['value' => clean($request->proCatPrefix)]);
        $allSettings->where('key', 'product_sub_cat_prefix')->first()->update(['value' => clean($request->proSubCatPrefix)]);
        $allSettings->where('key', 'product_prefix')->first()->update(['value' => clean($request->productPrefix)]);
        $allSettings->where('key', 'exp_cat_prefix')->first()->update(['value' => clean($request->expCatPrefix)]);
        $allSettings->where('key', 'exp_sub_cat_prefix')->first()->update(['value' => clean($request->expSubCatPrefix)]);
        $allSettings->where('key', 'pur_prefix')->first()->update(['value' => clean($request->purchasePrefix)]);
        $allSettings->where('key', 'pur_return_prefix')->first()->update(['value' => clean($request->purchaseReturnPrefix)]);
        $allSettings->where('key', 'quotation_prefix')->first()->update(['value' => clean($request->quotationPrefix)]);
        $allSettings->where('key', 'invoice_prefix')->first()->update(['value' => clean($request->invoicePrefix)]);
        $allSettings->where('key', 'invoice_return_prefix')->first()->update(['value' => clean($request->invoiceReturnPrefix)]);
        $allSettings->where('key', 'adjustment_prefix')->first()->update(['value' => clean($request->adjustmentPrefix)]);
        $allSettings->where('key', 'default_currency')->first()->update(['value' => clean($request->currency['id'])]);
        $allSettings->where('key', 'default_language')->first()->update(['value' => clean($request->language)]);
        $allSettings->where('key', 'logo')->first()->update(['value' => $logoName]);
        $allSettings->where('key', 'logo_black')->first()->update(['value' => $blackLogoName]);
        $allSettings->where('key', 'small_logo')->first()->update(['value' => $smallLogoName]);
        $allSettings->where('key', 'favicon')->first()->update(['value' => $favicon]);
        $allSettings->where('key', 'copyright')->first()->update(['value' => clean($request->copyrightText)]);
        $allSettings->where(
            'key',
            'default_client_slug'
        )->first()->update(['value' => clean($request->defaultClient['slug'])]);
        $allSettings->where(
            'key',
            'default_account_slug'
        )->first()->update(['value' => clean($request->defaultAccount['slug'])]);
        $allSettings->where(
            'key',
            'default_vat_rate_slug'
        )->first()->update(['value' => clean($request->defaultVatRate['slug'])]);

        GeneralSetting::updateOrCreate(['key' =>  'invoice_thank_you_message'],[ 'display_name' => 'Invoice message', 'value' => clean($request->invoiceThankYouMessage)]);
        GeneralSetting::updateOrCreate(['key' =>  'tax_registration_number'],[ 'display_name' => 'Tax Registration Number', 'value' => clean($request->taxRegistrationNumber)]);

        return redirect()->back()->withSuccess('Settings updated successfully!');
    }

    public function getSMTPforTenant(){
        $smtp_credentials = tenant()->smtp;
        return  response()->json($smtp_credentials);
    }

    public function updateSMTPforTenant(MailConfigurationRequest $request){
        tenant()->update([
            'smtp' => [
                "mail_mailer" => $request->mail_mailer,
                "mail_host" => $request->mail_host,
                "mail_port" => $request->mail_port,
                "mail_username" => $request->mail_username,
                "mail_password" => $request->mail_password,
                "mail_encryption" => $request->mail_encryption,
                "mail_from_address" => $request->mail_from_address,
                "mail_from_name" => $request->mail_from_name
            ]
        ]);

        // add activity log
        activity()
        ->causedBy(Auth::user())
        ->withProperties([
            'name' => "",
            'code' => "",
            'event' => 'Update'
        ])
        ->useLog('Mail Configuration Updated')
        ->log('Mail Configuration Updated');
        
        return 'Env SMTP updated successfully!';
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

    public function getSMSforTenant(){
        $sms_credentials = tenant()->sms;
        return  response()->json($sms_credentials);
    }

    public function updateSMSforTenant(SMSConfigurationRequest $request){
        tenant()->update([
            'sms' => [
                "twilio_account_sid" => $request->twilio_account_sid,
                "twilio_auth_token" => $request->twilio_auth_token,
                "twilio_from" => $request->twilio_from,
                "twilio_sms_service_sid" => $request->twilio_sms_service_sid
            ]
        ]);

            // add activity log
            activity()
            ->causedBy(Auth::user())
            ->withProperties([
                'name' => "",
                'code' => "",
                'event' => 'Update'
            ])
            ->useLog('SMS Configuration Updated')
            ->log('SMS Configuration Updated');
            
        return 'Env SMS updated successfully!';
    }

}