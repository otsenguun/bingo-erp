<?php

use Carbon\Carbon;
use App\Models\Currency;
use App\Models\GeneralSetting;
use App\Services\ImageService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\CurrencyResource;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\API\GeneralController;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

if (! function_exists('arrayToCollection')) {
    function arrayToCollection(array $array): Collection
    {
        return collect(json_decode(json_encode($array), false));
    }
}

if (! function_exists('getActivePaymentMethods')) {
    function getActivePaymentMethods(): Collection
    {
        return arrayToCollection(array_filter(config('payment-methods'), function ($paymentMethod) {
            return $paymentMethod['is_active'];
        }));
    }
}



if (! function_exists('public_tenant_path')) {
    function public_tenant_path($value)
    {
        return Storage::disk('tenant-public')->url(config('tenancy.filesystem.suffix_base').tenant()->id.'/'.$value);
    }
}


if (! function_exists('store_in_tenant')) {
    function store_in_tenant($path,$image,$extension)
    {
        $partition = config('tenancy.filesystem.suffix_base').tenant()->id;
        $newPath = Storage::disk('tenant-public')->putFileAs(
            "$partition/$path",
            $image,
            Carbon::now()->format('YmdHis').".$extension"
        );
        return str_replace($partition, '', $newPath);
    }
}


if (! function_exists('formatCurrency')) {
    function formatCurrency($value)
    {
        $currencyPosition = config('config.currencyPosition');
        $currencySymbol = config('config.currencySymbol');
        if ($currencyPosition == 'left') {
            return $currencySymbol . number_format($value, 2, '.', ',');
        }else{
            return number_format($value, 2, '.', ',') .  $currencySymbol;
        }
    }

}

function getGeneralSettingsInfo()
{
    $query = GeneralSetting::get();
    $settings = [
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
        'currency' => new CurrencyResource(Currency::where(
            'id',
            (int) $query->where('key', 'default_currency')->first()->value
        )->first()),
        'language' => $query->where('key', 'default_language')->first()->value,
        'logo' => asset('images/' . $query->where('key', 'logo')->first()->value),
        'blackLogo' => asset('images/' . $query->where('key', 'logo_black')->first()->value),
        'smallLogo' => asset('images/' . $query->where('key', 'small_logo')->first()->value),
        'favicon' => asset('images/' . $query->where('key', 'favicon')->first()->value),
        'copyright' => $query->where('key', 'copyright')->first()->value,
        'defaultClientSlug' => $query->where('key', 'default_client_slug')->first()->value,
        'defaultAccountSlug' => $query->where('key', 'default_account_slug')->first()->value,
        'defaultVatRateSlug' => $query->where('key', 'default_vat_rate_slug')->first()->value,
    ];

    return $settings;
}


function centralCurrencySymbolFormat($amount){
    $paymentController = new PaymentController();
    $centralActiveCurrency = $paymentController->centralActiveCurrency();

     // Format the amount based on the central currency position
     $currencyPosition = $centralActiveCurrency->position;
     if ($currencyPosition === 'left') {
         $formattedPendingAmount = $centralActiveCurrency->symbol . number_format($amount, 2);
     } else {
         $formattedPendingAmount = number_format($amount, 2) . $centralActiveCurrency->symbol;
     }
     return $formattedPendingAmount;
}


function centralCurrencyCodeFormat($amount){
    $paymentController = new PaymentController();
    $centralActiveCurrency = $paymentController->centralActiveCurrency();

     // Format the amount based on the central currency position
     $currencyPosition = $centralActiveCurrency->position;
     if ($currencyPosition === 'left') {
         $formattedPendingAmount = $centralActiveCurrency->code . ' ' . number_format($amount, 2);
     } else {
         $formattedPendingAmount = number_format($amount, 2) . ' ' . $centralActiveCurrency->code;
     }
     return $formattedPendingAmount;
}

function getPrefix(){
    $imageService = new ImageService();
    $generalController = new GeneralController($imageService);
    $getGeneralSettings = $generalController->getGeneralSettings();
    return $getGeneralSettings;
}


function getBase64Extension($base64Src){
    $pattern = '/^data:image\/[^;]+;base64,/';

    if (preg_match($pattern, $base64Src, $matches)) {
        return explode(';',explode('/', $matches[0])[1])[0];
    }
    return null;
}



// save multiple images
function saveMultipleImages($model, $request, $collectionName = 'default')
{
    if (!empty($request->images)) {
        foreach ($request->images as $index => $image) {
            $extension = getBase64Extension($image['path']);
            if($extension == null){
                continue;
            }

            $model
                ->addMediaFromBase64($image['path'])
                ->withCustomProperties([
                    'path' => $collectionName,
                    'highlight' => isset($image['highlight'])? $image['highlight']: 0,
                    'default' => isset($image['default'])? $image['default']: 0,

                ])
                ->usingFileName(Carbon::now()->format('YmdHis').".$extension")
                ->toMediaCollection($collectionName);
        }
    }
}


function saveBase64Image($model, $image, $collectionName = 'default')
{
    if (!empty($image)) {
        $extension = getBase64Extension($image['path']);
        if($extension == null){
            return;
        }

        $model
            ->addMediaFromBase64($image['path'])
            ->withCustomProperties([
                'path' => $collectionName,
                'highlight' => isset($image['highlight'])? $image['highlight']: 0,
                'default' => isset($image['default'])? $image['default']: 0,

            ])
            ->usingFileName(Carbon::now()->format('YmdHis').".$extension")
            ->toMediaCollection($collectionName);
    }
}



// update multiple images
function updateMultipleImages($model, $request, $collectionName = 'default')
{
    // Delete unwanted images
    foreach($request->deleted_image_uuids as $uuid){
        Media::where('uuid',$uuid)->delete();
    }

    // Process new and updated images
    foreach ($request->images as $index => $image) {
        $existingImage = Media::where('uuid',isset($image['uuid'])?$image['uuid']:null)->first();
        if ($existingImage) {
            $search_string = "data:image";
            
            if(preg_match("/$search_string/", $image['path'])){
                saveBase64Image($model, $image, $collectionName);
                $existingImage->delete();
            }else{
                $existingImage->custom_properties = [
                    'path' => $collectionName,
                    'highlight' => $image['highlight'],
                    'default' =>$image['default'],
                ];
                $existingImage->save();
            }

        }else{
            saveBase64Image($model, $image, $collectionName);
        }
    }
}

function isWindowsOS() {
    return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
}

function windowsTestSubHostReg($domainTenant){
    if (isWindowsOS()) {
        $hostEntry = "127.0.0.1      $domainTenant";
        $filePath = "C:\\Windows\\System32\\drivers\\etc\\hosts";
    
        // Check if the host entry is already present
        $hostsFileContent = file_get_contents($filePath);
        if (strpos($hostsFileContent, $hostEntry) === false) {
            $command = 'powershell.exe -Command "Start-Process cmd -ArgumentList \'/c echo ' . $hostEntry . ' >> ' . $filePath . '\' -Verb RunAs"';
            shell_exec($command);
        }
    }
}

function invoiceThankYouMessage(){
    return $message = GeneralSetting::where('key', 'invoice_thank_you_message')->first()->value ?? null;
 }

 if (!function_exists('handleGeneralSettingsImage')) {
    function handleGeneralSettingsImage($imageData, $existingImageName, $imagePrefix)
        {
            if ($existingImageName) {
                @unlink(public_path('images/' . $existingImageName));
            }
        
            $imageExtension = explode(
                '/',
                explode(':', substr($imageData, 0, strpos($imageData, ';')))[1]
            )[1];
            $currentDateTime = date('Ymd_His');
            $imageName = $imagePrefix . '_' . $currentDateTime . '.' . $imageExtension;
        
            if ($imageExtension === 'svg' || $imageExtension === 'svg+xml') {
                $imageName = $imagePrefix . '_' . $currentDateTime . '.' . 'svg';
                $imageData = explode(',', $imageData)[1];
                File::put(public_path('images/') . $imageName, base64_decode($imageData));
            } else {
                Image::make($imageData)->save(public_path('images/') . $imageName);
            }
        
            return $imageName;
        }
}
