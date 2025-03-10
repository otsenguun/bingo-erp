<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Currency;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\CurrencyResource;
use App\Http\Requests\Currency\StoreCurrencyRequest;
use App\Http\Requests\Currency\UpdateCurrencyRequest;

class CurrencyController extends Controller
{
    // define middleware
    public function __construct()
    {
        $this->middleware('can:currencies-management', ['except' => ['allCurrencies']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return CurrencyResource::collection(Currency::latest()->paginate($request->perPage));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCurrencyRequest $request)
    {
        // save currency
      $currency =  Currency::create([
            'name' => $request->name,
            'code' => $request->code,
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
                'name' => "",
                'code' => '[' . $request->name . ']',
                'event' => 'Create'
            ])
            ->useLog('Currency Created')
            ->log('Currency Created');

        return $this->responseWithSuccess('Currency added successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        try {
            $currency = Currency::where('slug', $slug)->first();

            return new CurrencyResource($currency);
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCurrencyRequest $request, $slug)
    {
        $currency = Currency::where('slug', $slug)->first();

        try {
            // update currency
            $currency->update([
                'name' => $request->name,
                'code' => $request->code,
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
                'name' => "",
                'code' => '[' . $request->name . ']',
                'event' => 'Update'
            ])
            ->useLog('Currency Updated')
            ->log('Currency Updated');

            return $this->responseWithSuccess('Currency updated successfully');
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($slug)
    {
        try {
            $currency = Currency::where('slug', $slug)->first();

            // add activity log
            activity()
                ->causedBy(Auth::user())
                ->performedOn($currency)
                ->withProperties([
                    'name' => "",
                    'code' => '[' . $currency->name . ']',
                    'event' => 'Delete'
                ])
                ->useLog('Currency Deleted')
                ->log('Currency Deleted');


            $currency->delete();

            return $this->responseWithSuccess('Currency deleted successfully');
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    /**
     * search resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $term = $request->term;

        $query = Currency::where('name', 'LIKE', '%'.$term.'%')
            ->orWhere('code', 'LIKE', '%'.$term.'%')
            ->orWhere('note', 'LIKE', '%'.$term.'%')
            ->latest()->paginate($request->perPage);

        return CurrencyResource::collection($query);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function allCurrencies()
    {
        $currencies = Currency::where('status', 1)->latest()->get();

        return CurrencyResource::collection($currencies);
    }
}
