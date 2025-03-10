<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Rules\MinOne;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\PurchaseReturn;
use App\Models\AccountTransaction;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\PurchaseReturnProduct;
use App\Interfaces\ITransactionService;
use App\Http\Resources\PurchaseReturnResource;
use App\Http\Resources\PurchaseReturnListReource;
use App\Http\Requests\Purchase\StorePurchaseReturnRequest;
use App\Http\Requests\Purchase\UpdatePurchaseReturnRequest;

class PurchaseReturnController extends Controller
{

    protected ITransactionService $transactionService;
    // define middleware
    public function __construct(ITransactionService $transactionService)
    {
        $this->middleware('can:purchase-return-list', ['only' => ['index', 'search']]);
        $this->middleware('can:purchase-return-create', ['only' => ['create']]);
        $this->middleware('can:purchase-return-view', ['only' => ['show']]);
        $this->middleware('can:purchase-return-edit', ['only' => ['update']]);
        $this->middleware('can:purchase-return-delete', ['only' => ['destroy']]);

        $this->transactionService = $transactionService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return PurchaseReturnListReource::collection(PurchaseReturn::with('purchase.supplier')->latest()->paginate($request->perPage));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StorePurchaseReturnRequest $request)
    {

        try {
            DB::beginTransaction();

            // get logged in user id
            $userId = auth()->user()->id;

            // generate code
            $code = 1;
            $prevCode = PurchaseReturn::latest()->first();
            if ($prevCode) {
                $code = $prevCode->code + 1;
            }

            // store retrun amount
            $isPaid = 0;
            $transactionID = null;
            if ($request->returnAmount > 0) {
                $transaction = $this->transactionService->createTransactionFromPurchaseReturn($request, $userId, $code);
                $isPaid = 1;
                $transactionID = $transaction->id;
            }

            // store return
            $purchaseReturn = PurchaseReturn::create([
                'reason' => $request->returnReason,
                'purchase_id' => $request->purchase['id'],
                'transaction_id' => $transactionID,
                'code' => $code,
                'total_return' => $request->totalReturn,
                'date' => $request->date,
                'note' => clean($request->note),
                'created_by' => $userId,
                'status' => $request->status,
            ]);

            // store return products
            foreach ($request->selectedProducts as $key => $selectedProduct) {
                $returnQty = (int) $selectedProduct['returnQty'];
                if ($returnQty > 0) {
                    $product = Product::where('slug', $selectedProduct['slug'])->first();
                    // calculate new purchase price
                    $currentStockPrice = $product->inventory_count * $product->purchase_price;

                    // purchase stock price
                    $purchaseStockPrice = $returnQty * $selectedProduct['purchasePrice'];
                    $totalStockPrice = $currentStockPrice - $purchaseStockPrice;
                    $totalQty = $product->inventory_count - $returnQty;
                    $unitCost = $totalStockPrice / $totalQty;

                    // update product purchase price
                    $product->update([
                        'purchase_price' => $unitCost,
                        'inventory_count' => $totalQty,
                    ]);

                    PurchaseReturnProduct::create([
                        'return_id' => $purchaseReturn->id,
                        'product_id' => $selectedProduct['id'],
                        'purchase_price' => $selectedProduct['purchasePrice'],
                        'quantity' => $returnQty,
                    ]);
                }
            }

            // update purchase status
            $purchaseReturn->purchase->update([
                'is_paid' => $isPaid,
            ]);

            // add activity log
            activity()
                ->causedBy(Auth::user())
                ->performedOn($purchaseReturn)
                ->withProperties([
                    'name' => "",
                    'code' => '[' . config('config.purchaseReturnPrefix') . '-' . $code . ']',
                    'event' => 'Create',
                    'slug' => $purchaseReturn->slug,
                    'routeName' => 'purchaseReturns.show'
                ])
                ->useLog('Purchase return Created')
                ->log('Purchase return Created');

            DB::commit();

            return $this->responseWithSuccess('Purchase return added successfully', [
                'slug' => $purchaseReturn->slug,
            ]);
        } catch (Exception $e) {
            DB::rollback();
            return $this->responseWithError($e->getMessage());
        }
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
            $purchaseReturn = PurchaseReturn::where('slug', $slug)->with('purchase.supplier', 'purchaseReturnProducts.purchaseReturn', 'purchaseReturnProducts.product.proSubCategory.category', 'purchaseReturnProducts.product.productUnit', 'purchaseReturnProducts.product.productTax', 'user', 'returnTransaction.cashbookAccount')->first();

            return new PurchaseReturnResource($purchaseReturn);
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdatePurchaseReturnRequest $request, $slug)
    {
        $purchaseReturn = PurchaseReturn::where('slug', $slug)
                                        ->with('purchase.supplier', 'purchaseReturnProducts.product.proSubCategory.category', 'purchaseReturnProducts.product.productUnit', 'returnTransaction.cashbookAccount')
                                        ->first();

        try {
            DB::beginTransaction();

            // store retrun amount
            $isPaid = 0;
            $transactionID = null;
            if ($request->returnAmount > 0) {
                if (isset($purchaseReturn->returnTransaction)) {
                    // update transaction
                    $transaction = $purchaseReturn->returnTransaction->update([
                        'account_id' => $request->account['id'],
                        'amount' => $request->returnAmount,
                        'transaction_date' => $request->date,
                        'cheque_no' => $request->chequeNo,
                        'receipt_no' => $request->receiptNo,
                        'status' => $request->status,
                    ]);
                    $transactionID = $purchaseReturn->returnTransaction->id;
                } else {
                    // get logged in user id
                    $userId = auth()->user()->id;
                    $reason = '['.config('config.purchaseReturnPrefix').'-'.$purchaseReturn->code.'] Purchase Return receivable added to ['.$request->account['accountNumber'].']';
                    // create transaction
                    $transaction = AccountTransaction::create([
                        'reason' => $reason,
                        'account_id' => $request->account['id'],
                        'amount' => $request->returnAmount,
                        'transaction_date' => $request->date,
                        'type' => 1,
                        'cheque_no' => $request->chequeNo,
                        'receipt_no' => $request->receiptNo,
                        'status' => $request->status,
                        'created_by' => $userId,
                    ]);
                    $transactionID = $transaction->id;
                }
                $isPaid = 1;
            } else {
                if (isset($purchaseReturn->returnTransaction)) {
                    $purchaseReturn->returnTransaction->delete();
                }
            }

            // update return
            $purchaseReturn->update([
                'reason' => $request->returnReason,
                'transaction_id' => $transactionID,
                'total_return' => $request->totalReturn,
                'date' => $request->date,
                'note' => clean($request->note),
                'status' => $request->status,
            ]);

            // delete return products and store new return products
            $purchaseReturn->purchaseReturnProducts->each->delete();
            foreach ($request->selectedProducts as $key => $selectedProduct) {

                // variable casting
                $returnedQty = (float) $selectedProduct['returnQty'];
                $oldQty = (float) $selectedProduct['oldReturnedQty'];
                $purchasePrice = (float) $selectedProduct['price'];

                $product = Product::where('slug', $selectedProduct['slug'])->first();
                // calculate new purchase price
                $currentStockPrice = $product->inventory_count * $product->purchase_price;

                // purchase stock price
                $returnedStockPrice = ($oldQty - $returnedQty) * $purchasePrice;
                $totalStockPrice = $currentStockPrice + $returnedStockPrice;
                $totalQty = $product->inventory_count + $oldQty - $returnedQty;
                $unitCost = null;
                if ($totalQty > 0) {
                    $unitCost = $totalStockPrice / $totalQty;
                }
                // update product purchase price
                $product->update([
                    'purchase_price' => $unitCost,
                    'inventory_count' => $totalQty,
                ]);

                PurchaseReturnProduct::create([
                    'return_id' => $purchaseReturn->id,
                    'product_id' => $selectedProduct['id'],
                    'purchase_price' => $selectedProduct['price'],
                    'quantity' => $returnedQty,
                ]);
            }


            // add activity log
            activity()
                ->causedBy(Auth::user())
                ->performedOn($purchaseReturn)
                ->withProperties([
                    'name' => "",
                    'code' => '[' . config('config.purchaseReturnPrefix') . '-' . $purchaseReturn->code . ']',
                    'event' => 'Update',
                    'slug' => $purchaseReturn->slug,
                    'routeName' => 'purchaseReturns.show'
                ])
                ->useLog('Purchase return Updated')
                ->log('Purchase return Updated');

            // update purchase status
            $purchaseReturn->purchase->update([
                'is_paid' => $isPaid,
            ]);

            DB::commit();

            return $this->responseWithSuccess('Purchase return update added successfully', [
                'slug' => $purchaseReturn->slug,
            ]);
        } catch (Exception $e) {
            DB::rollback();
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

            $purchaseReturn = PurchaseReturn::where('slug', $slug)->with('purchaseReturnProducts.product', 'purchase', 'returnTransaction')->first();
            // update purchase
            $purchaseReturn->purchase->update([
                'is_paid' => $purchaseReturn->purchase->totalDue() == 0 ? 1 : 0,
            ]);


            // add activity log
            activity()
                ->causedBy(Auth::user())
                ->performedOn($purchaseReturn)
                ->withProperties([
                    'name' => "",
                    'code' => '[' . config('config.purchaseReturnPrefix') . '-' . $purchaseReturn->code . ']',
                    'event' => 'Delete'
                ])
                ->useLog('Purchase return Deleted')
                ->log('Purchase return Deleted');

            // delete return
            $purchaseReturn->delete();

            DB::commit();

            return $this->responseWithSuccess('Purchase return deleted successfully');
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
        $query = PurchaseReturn::with('purchase.supplier');

        if ($request->startDate && $request->endDate) {
            $query = $query->whereBetween('date', [$request->startDate, $request->endDate]);
        }

        $query = $query->where(function ($query) use ($term) {
            $query->where('reason', 'LIKE', '%'.$term.'%')
                ->orWhere('slug', 'LIKE', '%'.$term.'%')
                ->orWhere('code', 'LIKE', '%'.$term.'%')
                ->orWhere('total_return', 'LIKE', '%'.$term.'%')
                ->orWhereHas('purchase', function ($newQuery) use ($term) {
                    $newQuery->where('purchase_no', 'LIKE', '%'.$term.'%')
                        ->orWhere('po_reference', 'LIKE', '%'.$term.'%')
                        ->orWhereHas('supplier', function ($anotherQuery) use ($term) {
                            $anotherQuery->where('name', 'LIKE', '%'.$term.'%')
                                ->orWhere('phone', 'LIKE', '%'.$term.'%');
                        });
                });
        });

        return PurchaseReturnListReource::collection($query->latest()->paginate($request->perPage));
    }
}
