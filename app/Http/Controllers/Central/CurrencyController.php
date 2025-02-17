<?php

namespace App\Http\Controllers\Central;

use Exception;
use Illuminate\Http\Request;
use App\Models\CentralCurrency;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\CentralCurrencyResource;

class CurrencyController extends Controller
{

    public function index(Request $request)
    {
        return CentralCurrencyResource::collection(CentralCurrency::latest()->paginate($request->perPage));
    }


    public function store(Request $request)
    {
        // validate request
        $this->validate($request, [
            'name' => 'required|string|max:50|unique:central_currencies',
            'code' => 'required|string|max:50|unique:central_currencies,code',
            'rate' => 'required',
            'symbol' => 'required|string|max:50',
            'note' => 'nullable|string|max:255',
        ]);
        // save currency
      $CentralCurrency =  CentralCurrency::create([
            'name' => $request->name,
            'code' => $request->code,
            'rate' => $request->rate,
            'symbol' => $request->symbol,
            'position' => $request->position,
            'note' => $request->note,
            'status' => $request->status,
        ]);

        // add activity log
        activity()
        ->causedBy(Auth::user())
        ->performedOn($CentralCurrency)
        ->withProperties([
            'name' => $request->name,
            'code' =>  '[' . $request->code . ']',
            'event' => 'Create',
            'slug' => '/',
            'routeName' => 'setup.centralCurrency.index'
        ])
        ->useLog('Currency Created')
        ->log('Currency Created');

        return $this->responseWithSuccess('Currency added successfully');
    }

    public function show($slug)
    {
        try {
            $currency = CentralCurrency::where('slug', $slug)->first();

            return new CentralCurrencyResource($currency);
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    public function update(Request $request, $slug)
    {
        $currency = CentralCurrency::where('slug', $slug)->first();
        // validate request
        $this->validate($request, [
            'name' => 'required|string|max:50|unique:central_currencies,name,' . $currency->id,
            'code' => 'required|string|max:50|unique:central_currencies,code,' . $currency->id,
            'rate' => 'required',
            'symbol' => 'required|string|max:50',
            'note' => 'nullable|string|max:255',
        ]);

        try {
            // update currency
            $currency->update([
                'name' => $request->name,
                'code' => $request->code,
                'rate' => $request->rate,
                'symbol' => $request->symbol,
                'position' => $request->position,
                'note' => $request->note,
                'status' => $request->status,
            ]);

            // add activity log
            activity()
            ->causedBy(Auth::user())
            ->performedOn($currency)
            ->withProperties([
                'name' => $request->name,
                'code' =>  '[' . $request->code . ']',
                'event' => 'Update',
                'slug' => '/',
                'routeName' => 'setup.centralCurrency.index'
            ])
            ->useLog('Currency Updated')
            ->log('Currency Updated');

            return $this->responseWithSuccess('Currency updated successfully');
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    public function destroy($slug)
    {
        try {
            $currency = CentralCurrency::where('slug', $slug)->first();

            // add activity log
            activity()
            ->causedBy(Auth::user())
            ->performedOn($currency)
            ->withProperties([
                'name' => $currency->name,
                'code' =>  '[' . $currency->code . ']',
                'event' => 'Delete',
                'slug' => '',
                'routeName' => ''
            ])
            ->useLog('Currency Deleted')
            ->log('Currency Deleted');

            $currency->delete();

            return $this->responseWithSuccess('Currency deleted successfully');
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }


    public function allCurrencies()
    {
        $currencies = CentralCurrency::where('status', 1)->latest()->get();

        return CentralCurrencyResource::collection($currencies);
    }
}
