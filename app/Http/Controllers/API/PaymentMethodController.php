<?php

namespace App\Http\Controllers\API;

use Exception;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\PaymentMethodResource;
use App\Http\Requests\PaymentMethod\StorePaymentMethodRequest;
use App\Http\Requests\PaymentMethod\UpdatePaymentMethodRequest;

class PaymentMethodController extends Controller
{
    // define middleware
    public function __construct()
    {
        $this->middleware('can:payment-method-management', ['except' => ['allMethods']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return PaymentMethodResource::collection(PaymentMethod::latest()->paginate($request->perPage));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePaymentMethodRequest $request)
    {
        // save payment method
       $paymentMethod = PaymentMethod::create([
            'name' => $request->name,
            'code' => $request->shortCode,
            'note' => $request->note,
            'status' => $request->status,
        ]);

        // add activity log
        activity()
            ->causedBy(Auth::user())
            ->performedOn($paymentMethod)
            ->withProperties([
                'name' => "",
                'code' => '[' . $request->name . ']',
                'event' => 'Create'
            ])
            ->useLog('Payment Method Created')
            ->log('Payment Method Created');

        return $this->responseWithSuccess('Payment method added successfully');
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
            $method = PaymentMethod::where('slug', $slug)->first();

            return $method;
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
    public function update(UpdatePaymentMethodRequest $request, $slug)
    {
        $method = PaymentMethod::where('slug', $slug)->first();

        try {
            // update payment method
            $method->update([
                'name' => $request->name,
                'code' => $request->shortCode,
                'note' => $request->note,
                'status' => $request->status,
            ]);

            // add activity log
            activity()
                ->causedBy(Auth::user())
                ->performedOn($method)
                ->withProperties([
                    'name' => "",
                    'code' => '[' . $request->name . ']',
                    'event' => 'Update'
                ])
                ->useLog('Payment Method Updated')
                ->log('Payment Method Updated');

            return $this->responseWithSuccess('Payment method updated successfully');
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
            $method = PaymentMethod::where('slug', $slug)->first();

            // add activity log
            activity()
                ->causedBy(Auth::user())
                ->performedOn($method)
                ->withProperties([
                    'name' => "",
                    'code' => '[' . $method->name . ']',
                    'event' => 'Delete'
                ])
                ->useLog('Payment Method Deleted')
                ->log('Payment Method Deleted');


            $method->delete();

            return $this->responseWithSuccess('Payment method deleted successfully');
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

        $query = PaymentMethod::where('name', 'LIKE', '%'.$term.'%')
            ->orWhere('code', 'LIKE', '%'.$term.'%')
            ->orWhere('note', 'LIKE', '%'.$term.'%')
            ->latest()->paginate($request->perPage);

        return PaymentMethodResource::collection($query);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function allMethods()
    {
        $methods = PaymentMethod::where('status', 1)->latest()->get();

        return PaymentMethodResource::collection($methods);
    }
}