<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Loan;
use Illuminate\Http\Request;
use App\Services\ImageService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\LoanResource;
use Illuminate\Support\Facades\Auth;
use App\Interfaces\ITransactionService;
use App\Http\Requests\Loan\StoreLoanRequest;
use App\Http\Requests\Loan\UpdateLoanRequest;

class LoanController extends Controller
{

    protected ITransactionService $transactionService;

    protected ImageService $imageService;

    // define middleware
    public function __construct(ITransactionService $transactionService)
    {
        $this->middleware('can:loan-list', ['only' => ['index', 'search']]);
        $this->middleware('can:loan-create', ['only' => ['create']]);
        $this->middleware('can:loan-view', ['only' => ['show']]);
        $this->middleware('can:loan-edit', ['only' => ['update']]);
        $this->middleware('can:loan-delete', ['only' => ['destroy']]);

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
        return LoanResource::collection(Loan::with('loanAuthority', 'loanPayments', 'loanTransaction.cashbookAccount', 'user')->latest()->paginate($request->perPage));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreLoanRequest $request)
    {

        try {
            DB::beginTransaction();

            $userID = auth()->user()->id;

            // upload thumbnail and set the name
            $imageName = '';
            if ($request->image) {
                $imageName = $this->imageService->uploadImageAndGetPath($request->image, 'loans');
            }

            $transaction = $this->transactionService->createTransactionFromLoan($request, $userID);

            // create loan
          $loan =  Loan::create([
                'reason' => $request->reason,
                'reference_no' => $request->referenceNo,
                'authority_id' => $request->authority['id'],
                'transaction_id' => $transaction->id,
                'interest' => $request->interest,
                'payable' => $request->rowPayableAMount,
                'loan_type' => $request->loanType,
                'payment_type' => $request->paymentType,
                'duration' => $request->duration,
                'date' => $request->date,
                'created_by' => $userID,
                'note' => clean($request->note),
                'image_path' => $imageName,
                'status' => $request->status,
            ]);

            // add activity log
            activity()
                ->causedBy(Auth::user())
                ->performedOn($loan)
                ->withProperties([
                    'name' => "",
                    'code' => '[' . $request->reason . ']',
                    'event' => 'Create',
                    'slug' => $loan->slug,
                    'routeName' => 'loans.show'
                ])
                ->useLog('Loan Created')
                ->log('Loan Created');

            DB::commit();

            return $this->responseWithSuccess('Loan added successfully');
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
            $loan = Loan::with('loanAuthority', 'loanPayments.loanPaymentTransaction.cashbookAccount', 'loanTransaction.cashbookAccount', 'user')->where('slug', $slug)->first();

            return new LoanResource($loan);
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
    public function update(UpdateLoanRequest $request, $slug)
    {
        $loan = Loan::where('slug', $slug)->first();

        try {
            DB::beginTransaction();

            // upload thumbnail and set the name
            $imageName = $loan->image_path;
            if ($request->image) {
                $imageName = $this->imageService->uploadImageAndGetPath($request->image, 'loans');
                $this->imageService->checkImageExistsAndDelete($loan->image_path,'loans');
            }

            // store transaction
            $loan->loanTransaction->update([
                'amount' => $request->amount,
                'transaction_date' => $request->date,
                'status' => $request->status,
            ]);

            // update loan
            $loan->update([
                'reason' => $request->reason,
                'reference_no' => $request->referenceNo,
                'authority_id' => $request->authority['id'],
                'interest' => $request->interest,
                'payable' => $request->rowPayableAMount,
                'payment_type' => $request->paymentType,
                'duration' => $request->duration,
                'date' => $request->date,
                'note' => clean($request->note),
                'image_path' => $imageName,
                'status' => $request->status,
            ]);

            // add activity log
            activity()
                ->causedBy(Auth::user())
                ->performedOn($loan)
                ->withProperties([
                    'name' => "",
                    'code' => '[' . $request->reason . ']',
                    'event' => 'Update',
                    'slug' => $loan->slug,
                    'routeName' => 'loans.show'
                ])
                ->useLog('Loan Updated')
                ->log('Loan Updated');

            DB::commit();

            return $this->responseWithSuccess('Loan updated successfully');
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

            $loan = Loan::with('loanPayments')->where('slug', $slug)->first();
            if ($loan->payable < $loan->loanTransaction->cashbookAccount->availableBalance()) {
                // delete image from storage
                if ($loan->image_path) {
                    $this->imageService->checkImageExistsAndDelete($loan->image_path,'loans');
                }
                // delete loan payment images
                if ($loan->loanPayments) {
                    foreach ($loan->loanPayments as $loanPayment) {
                        if ($loanPayment->image_path) {
                            @unlink(public_path('images/loan-payments/'.$loanPayment->image_path));
                            $loanPayment->loanPaymentTransaction->delete();
                        }
                    }
                }

                // add activity log
                activity()
                    ->causedBy(Auth::user())
                    ->performedOn($loan)
                    ->withProperties([
                        'name' => "",
                        'code' => '[' . $loan->reason . ']',
                        'event' => 'Delete'
                    ])
                    ->useLog('Loan Deleted')
                    ->log('Loan Deleted');


                $loan->loanTransaction->delete();
                $loan->delete();


                return $this->responseWithSuccess('Loan deleted successfully');
            }

            DB::commit();

            return $this->responseWithError('Loan can\'t be remove');
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
        $query = Loan::with('loanAuthority', 'loanTransaction.cashbookAccount', 'user');

        if ($request->startDate && $request->endDate) {
            $query = $query->whereBetween('date', [$request->startDate, $request->endDate]);
        }

        $query->where(function ($query) use ($term) {
            $query->where('reason', 'LIKE', '%'.$term.'%')
                ->orWhere('reference_no', 'LIKE', '%'.$term.'%')
                ->orWhereHas('loanAuthority', function ($newQuery) use ($term) {
                    $newQuery->where('name', 'LIKE', '%'.$term.'%');
                })
                ->orWhereHas('loanTransaction', function ($newQuery) use ($term) {
                    $newQuery->where('amount', '=', $term)
                        ->orWhereHas('cashbookAccount', function ($newQuery) use ($term) {
                            $newQuery->where('account_number', 'LIKE', '%'.$term.'%')->orWhere('bank_name', 'LIKE', '%'.$term.'%')
                                ->orWhere('bank_name', 'LIKE', '%'.$term.'%');
                        });
                });
        });

        return LoanResource::collection($query->latest()->paginate($request->perPage));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function allLoans()
    {
        $loans = Loan::with('loanAuthority', 'loanPayments', 'loanTransaction.cashbookAccount', 'user')->where('status', 1)->where('is_paid', 0)->latest()->get();

        return LoanResource::collection($loans);
    }
}
