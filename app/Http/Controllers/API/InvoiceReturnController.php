<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Rules\MinOne;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\InvoiceReturn;
use App\Models\AccountTransaction;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\InvoiceReturnProduct;
use Illuminate\Support\Facades\Auth;
use App\Interfaces\ITransactionService;
use App\Http\Resources\InvoiceReturnResource;
use App\Http\Resources\InvoiceReturnListResource;
use App\Http\Requests\Invoice\StoreInvoiceReturnRequest;
use App\Http\Requests\Invoice\UpdateInvoiceReturnRequest;

class InvoiceReturnController extends Controller
{
    protected ITransactionService $transactionService;
    // define middleware
    public function __construct(ITransactionService $transactionService)
    {
        $this->middleware('can:invoice-return-list', ['only' => ['index', 'search']]);
        $this->middleware('can:invoice-return-create', ['only' => ['create']]);
        $this->middleware('can:invoice-return-view', ['only' => ['show']]);
        $this->middleware('can:invoice-return-edit', ['only' => ['update']]);
        $this->middleware('can:invoice-return-delete', ['only' => ['destroy']]);

        $this->transactionService = $transactionService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return InvoiceReturnListResource::collection(InvoiceReturn::with('invoice.client',
            'user')->latest()->paginate($request->perPage));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreInvoiceReturnRequest $request)
    {

        try {
            DB::beginTransaction();

            // generate code
            $code = 1;
            $lastReturn = InvoiceReturn::latest()->first();
            if ($lastReturn) {
                $code = $lastReturn->return_no + 1;
            }

            // get logged in user id
            $userId = auth()->user()->id;

            // store retrun amount
            $isPaid = 0;
            $transactionID = null;
            if ($request->returnAmount > 0) {

                $transaction = $this->transactionService->createTransactionFromInvoiceReturn($request, $userId, $code);

                $isPaid = 1;
                $transactionID = $transaction->id;
            }

            // store invoice return
            $invoiceReturn = InvoiceReturn::create([
                'reason' => $request->returnReason,
                'return_no' => $code,
                'invoice_id' => $request->invoice['id'],
                'total_return' => $request->totalReturn,
                'date' => $request->date,
                'note' => clean($request->note),
                'transaction_id' => $transactionID,
                'created_by' => $userId,
                'status' => $request->status,
            ]);

            // update invoice
            $invoiceReturn->invoice->update([
                'discount' => $request->invoiceDiscount,
                'is_paid' => $isPaid,
            ]);

            // store invoice products
            foreach ($request->selectedProducts as $key => $selectedProduct) {
                // update product inventory
                $product = Product::where('slug', $selectedProduct['slug'])->first();
                $product->update([
                    'inventory_count' => $product->inventory_count + $selectedProduct['returnQty'],
                ]);

                // store return product
                if ($selectedProduct['returnQty'] > 0) {
                    InvoiceReturnProduct::create([
                        'return_id' => $invoiceReturn->id,
                        'product_id' => $selectedProduct['id'],
                        'sale_price' => $selectedProduct['unitCost'],
                        'purchase_price' => $selectedProduct['avgPurchasePrice'],
                        'quantity' => $selectedProduct['returnQty'],
                    ]);
                }
            }

            // add activity log
            activity()
                ->causedBy(Auth::user())
                ->performedOn($invoiceReturn)
                ->withProperties([
                    'name' => "",
                    'code' => '[' . config('config.invoiceReturnPrefix') . '-' . $code . ']',
                    'event' => 'Create',
                    'slug' => $invoiceReturn->slug,
                    'routeName' => 'invoiceReturns.show'
                ])
                ->useLog('Invoice Return Created')
                ->log('Invoice Return Created');

            DB::commit();

            return $this->responseWithSuccess('Invoice return added successfully', [
                'slug' => $invoiceReturn->slug,
            ]);
        } catch (Exception $e) {
            DB::rollback();
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
    public function update(UpdateInvoiceReturnRequest $request, $slug)
    {
        $invoiceReturn = InvoiceReturn::where('slug', $slug)->with('invoiceReturnProducts', 'user')->first();

        try {
            DB::beginTransaction();

            // store retrun amount
            $isPaid = 0;
            $transactionID = null;
            if ($request->returnAmount > 0) {
                if (isset($invoiceReturn->returnTransaction)) {
                    // update transaction
                    $transaction = $invoiceReturn->returnTransaction->update([
                        'account_id' => $request->account['id'],
                        'amount' => $request->returnAmount,
                        'transaction_date' => $request->date,
                        'cheque_no' => $request->chequeNo,
                        'receipt_no' => $request->receiptNo,
                        'status' => $request->status,
                    ]);
                    $transactionID = $invoiceReturn->returnTransaction->id;
                } else {
                    // get logged in user id
                    $userId = auth()->user()->id;
                    $reason = '['.config('config.purchaseReturnPrefix').'-'.$invoiceReturn->code.'] Invoice Return payable sent from ['.$request->account['accountNumber'].']';
                    // create transaction
                    $transaction = AccountTransaction::create([
                        'reason' => $reason,
                        'account_id' => $request->account['id'],
                        'amount' => $request->returnAmount,
                        'transaction_date' => $request->date,
                        'type' => 0,
                        'cheque_no' => $request->chequeNo,
                        'receipt_no' => $request->receiptNo,
                        'status' => $request->status,
                        'created_by' => $userId,
                    ]);
                    $transactionID = $transaction->id;
                }
                $isPaid = 1;
            } else {
                if (isset($invoiceReturn->returnTransaction)) {
                    $invoiceReturn->returnTransaction->delete();
                }
            }

            // update invoice return
            $invoiceReturn->update([
                'reason' => $request->returnReason,
                'transaction_id' => $transactionID,
                'total_return' => $request->totalReturn,
                'date' => $request->date,
                'note' => clean($request->note),
                'status' => $request->status,
            ]);

            // delete return products and store new return products
            $invoiceReturn->invoiceReturnProducts->each->delete();
            foreach ($request->selectedProducts as $key => $selectedProduct) {
                // update product inventory
                $product = Product::where('slug', $selectedProduct['slug'])->first();
                $product->update([
                    'inventory_count' => $product->inventory_count - $selectedProduct['oldQty'] + $selectedProduct['returnQty'],
                ]);

                // store product
                if ($selectedProduct['returnQty']) {
                    InvoiceReturnProduct::create([
                        'return_id' => $invoiceReturn->id,
                        'product_id' => $selectedProduct['id'],
                        'sale_price' => $selectedProduct['unitCost'],
                        'purchase_price' => $selectedProduct['purchasePrice'],
                        'quantity' => $selectedProduct['returnQty'],
                    ]);
                }
            }

            // update invoice
            $invoiceReturn->invoice->update([
                'discount' => $request->invoiceDiscount,
                'is_paid' => $isPaid,
            ]);

            // add activity log
            activity()
                ->causedBy(Auth::user())
                ->performedOn($invoiceReturn)
                ->withProperties([
                    'name' => "",
                    'code' => '[' . config('config.invoiceReturnPrefix') . '-' . $invoiceReturn->return_no . ']',
                    'event' => 'Update',
                    'slug' => $invoiceReturn->slug,
                    'routeName' => 'invoiceReturns.show'
                ])
                ->useLog('Invoice Return Updated')
                ->log('Invoice Return Updated');

            DB::commit();

            return $this->responseWithSuccess('Invoice return updated successfully');
        } catch (Exception $e) {
            DB::rollback();
            return $this->responseWithError($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return InvoiceReturnResource|\Illuminate\Http\JsonResponse
     */
    public function show($slug)
    {
        try {
            $invoiceReturn = InvoiceReturn::where('slug', $slug)
                ->with(
                    [
                        'invoice' => [
                            'client',
                        ],
                        'invoiceReturnProducts' => [
                            'invoiceReturn',
                            'product' => [
                                'productUnit',
                                'productTax',
                            ],
                        ],
                        'user'
                    ],
                )->firstOrFail();

            return new InvoiceReturnResource($invoiceReturn);
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
            DB::beginTransaction();

            $invoiceReturn = InvoiceReturn::where('slug', $slug)->with('invoiceReturnProducts.product', 'invoice',
                'returnTransaction')->first();
            // update invoice
            $invoice = $invoiceReturn->invoice;
            $isPaid = $invoiceReturn->invoice->totalDue() == 0 ? 1 : 0;
            $newDiscount = $invoice->discount;
            if ($invoice->discount_type == 1) {
                $newDiscount = ($invoice->discountPercentage() / 100) * $invoice->sub_total;
                $invoice->update([
                    'discount' => $newDiscount,
                    'is_paid' => $isPaid,
                ]);
            }

            // add activity log
            activity()
                ->causedBy(Auth::user())
                ->performedOn($invoiceReturn)
                ->withProperties([
                    'name' => "",
                    'code' => '[' . config('config.invoiceReturnPrefix') . '-' . $invoiceReturn->return_no . ']',
                    'event' => 'Delete'
                ])
                ->useLog('Invoice Return Deleted')
                ->log('Invoice Return Deleted');

            $invoiceReturn->delete();

            DB::commit();

            return $this->responseWithSuccess('Invoice return deleted successfully!');
        } catch (Exception $e) {
            DB::rollback();
            return $this->responseWithError($e->getMessage());
        }
    }

    /**
     * search resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function search(Request $request)
    {
        $term = $request->term;
        $query = InvoiceReturn::with('invoice.client', 'user');

        if ($request->startDate && $request->endDate) {
            $query = $query->whereBetween('date', [$request->startDate, $request->endDate]);
        }

        $query->where(function ($query) use ($term) {
            $query->where('reason', 'LIKE', '%'.$term.'%')
                ->orWhere('slug', 'LIKE', '%'.$term.'%')
                ->orWhereHas('invoice', function ($newQuery) use ($term) {
                    $newQuery->where('invoice_no', 'LIKE', '%'.$term.'%')
                        ->orWhere('po_reference', 'LIKE', '%'.$term.'%')
                        ->orWhereHas('client', function ($anotherQuery) use ($term) {
                            $anotherQuery->where('name', 'LIKE', '%'.$term.'%')
                                ->orWhere('client_id', 'LIKE', '%'.$term.'%');
                        });
                });
        });

        return InvoiceReturnListResource::collection($query->latest()->paginate($request->perPage));
    }
}
