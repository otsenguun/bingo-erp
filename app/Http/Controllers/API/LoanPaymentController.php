<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Loan;
use App\Models\LoanPayment;
use Illuminate\Http\Request;
use App\Services\ImageService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Interfaces\ITransactionService;
use App\Http\Resources\LoanPaymentResource;
use App\Http\Requests\Loan\StoreLoanPaymentRequest;
use App\Http\Requests\Loan\UpdateLoanPaymentRequest;

class LoanPaymentController extends Controller
{

    protected ITransactionService $transactionService;

    protected ImageService $imageService;

    // define middleware
    public function __construct(ITransactionService $transactionService)
    {
        $this->middleware('can:loan-payment-list', ['only' => ['index', 'search']]);
        $this->middleware('can:loan-payment-create', ['only' => ['create']]);
        $this->middleware('can:loan-payment-view', ['only' => ['show']]);
        $this->middleware('can:loan-payment-edit', ['only' => ['update']]);
        $this->middleware('can:loan-payment-delete', ['only' => ['destroy']]);

        $this->imageService = new ImageService();
        $this->transactionService = $transactionService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return LoanPaymentResource::collection(LoanPayment::with('loan.user', 'loanPaymentTransaction.cashbookAccount', 'loan.loanPayments.loanPaymentTransaction', 'loan.loanAuthority', 'loan.loanTransaction.cashbookAccount', 'user')->latest()->paginate($request->perPage));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreLoanPaymentRequest $request)
    {

        try {
            DB::beginTransaction();

            // get loan
            $loan = Loan::where('slug', $request->loan['slug'])->first();

            // upload thumbnail and set the name
            $imageName = '';
            if ($request->image) {
                $imageName = $this->imageService->uploadImageAndGetPath($request->image, 'loan-payments');
            }

            $userId = auth()->user()->id;

            $transaction = $this->transactionService->createTransactionFromLoanPayment($request, $userId);

            // create loan payment
            $loanPayment = LoanPayment::create([
                'reference_no' => $request->referenceNo,
                'loan_id' => $loan->id,
                'transaction_id' => $transaction->id,
                'amount' => $request->amount,
                'interest' => $request->interest,
                'date' => $request->date,
                'created_by' => $userId,
                'note' => $request->note,
                'image_path' => $imageName,
                'status' => $request->status,
            ]);

            // update loan
            if ($loan->totalDue() == 0 && $loanPayment->status == 1) {
                $loan->update([
                    'is_paid' => 1,
                ]);
            }

            // add activity log
            activity()
                ->causedBy(Auth::user())
                ->performedOn($loanPayment)
                ->withProperties([
                    'name' => "",
                    'code' => '[' . $loan->reason . ']',
                    'event' => 'Create',
                    'slug' => $loanPayment->slug,
                    'routeName' => 'loanPayments.show'
                ])
                ->useLog('Loan Payment Created')
                ->log('Loan Payment Created');

            DB::commit();

            return $this->responseWithSuccess('Loan payment added successfully');
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
            $loanPayment = LoanPayment::with('loanPaymentTransaction.cashbookAccount', 'loan.loanPayments.loanPaymentTransaction', 'loan.loanAuthority', 'loan.loanTransaction.cashbookAccount', 'user')->where('slug', $slug)->first();

            return new LoanPaymentResource($loanPayment);
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
    public function update(UpdateLoanPaymentRequest $request, $slug)
    {
        $loanPayment = LoanPayment::where('slug', $slug)->first();

        try {
            DB::beginTransaction();

            // get loan
            $loan = Loan::where('slug', $request->loan['slug'])->first();
            // upload thumbnail and set the name
            $imageName = $loanPayment->image_path;
            if ($request->image) {
                $imageName = $this->imageService->uploadImageAndGetPath($request->image, 'loan-payments');
                $this->imageService->checkImageExistsAndDelete($loanPayment->image_path);
            }

            $totalAmount = $request->amount + $request->interest;
            $reason = $request->loan['reference'].'Loan Paid';

            // update transaction
            $loanPayment->loanPaymentTransaction->update([
                'account_id' => $request->account['id'],
                'amount' => $totalAmount,
                'reason' => $reason,
                'transaction_date' => $request->date,
                'status' => $request->status,
            ]);

            // update loan payment
            $loanPayment->update([
                'reference_no' => $request->referenceNo,
                'date' => $request->date,
                'note' => $request->note,
                'amount' => $request->amount,
                'interest' => $request->interest,
                'image_path' => $imageName,
                'status' => $request->status,
            ]);

            // update loan
            if ($loan->totalDue() == 0 && $loanPayment->status == 1) {
                $loan->update([
                    'is_paid' => 1,
                ]);
            }

            // add activity log
            activity()
                ->causedBy(Auth::user())
                ->performedOn($loanPayment)
                ->withProperties([
                    'name' => "",
                    'code' => '[' . $loan->reason . ']',
                    'event' => 'Update',
                    'slug' => $loanPayment->slug,
                    'routeName' => 'loanPayments.show'
                ])
                ->useLog('Loan Payment Updated')
                ->log('Loan Payment Updated');

            DB::commit();

            return $this->responseWithSuccess('Loan payment updated successfully');
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

            $loanPayment = LoanPayment::where('slug', $slug)->first();
            // delete image from storage
            if ($loanPayment->image_path) {
                $this->imageService->checkImageExistsAndDelete($loanPayment->image_path);
            }

            // add activity log
            activity()
                ->causedBy(Auth::user())
                ->performedOn($loanPayment)
                ->withProperties([
                    'name' => "",
                    'code' => '[' . $loanPayment->reference_no . ']',
                    'event' => 'Delete'
                ])
                ->useLog('Loan Payment Deleted')
                ->log('Loan Payment Deleted');

            $loanPayment->loanPaymentTransaction->delete();
            $loanPayment->delete();

            DB::commit();

            return $this->responseWithSuccess('Loan payment deleted successfully');
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
        $query = LoanPayment::with('loan', 'loanPaymentTransaction.cashbookAccount');

        if ($request->startDate && $request->endDate) {
            $query = $query->whereBetween('date', [$request->startDate, $request->endDate]);
        }

        $query->where(function ($query) use ($term) {
            $query->where('reference_no', 'LIKE', '%'.$term.'%')
                ->orWhereHas('loan', function ($newQuery) use ($term) {
                    $newQuery->where('reason', 'LIKE', '%'.$term.'%')->orWhere('reference_no', 'LIKE', '%'.$term.'%');
                })
                ->orWhereHas('loanPaymentTransaction', function ($newQuery) use ($term) {
                    $newQuery->where('amount', '=', $term)
                        ->orWhereHas('cashbookAccount', function ($newQuery) use ($term) {
                            $newQuery->where('account_number', 'LIKE', '%'.$term.'%')
                                ->orWhere('bank_name', 'LIKE', '%'.$term.'%');
                        });
                });
        });

        return LoanPaymentResource::collection($query->latest()->paginate($request->perPage));
    }
}
