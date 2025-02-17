<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\Setting\UpdateAdvancedSettingsRequest;
use App\Models\Gateway;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\DomainResource;
use Jackiedo\DotenvEditor\Facades\DotenvEditor;
use Stancl\Tenancy\Database\Models\Domain;

class SubscriptionPaymentMethodController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index()
    {
        $paymentMethods = array_values(getActivePaymentMethods()->toArray());

        return $this->responseWithSuccess('Data retrieved successfully', $paymentMethods);
    }
}