<?php

namespace App\Http\Controllers\API;

use Exception;
use Illuminate\Http\Request;
use App\Models\LoanAuthority;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Loan\StoreLoanRequest;
use App\Http\Resources\LoanAuthorityResource;
use App\Http\Resources\AuthorityLoansResource;
use App\Http\Requests\Loan\StoreLoanAuthorityRequest;
use App\Http\Requests\Loan\UpdateLoanAuthorityRequest;

class LoanAuthorityController extends Controller
{
    // define middleware
    public function __construct()
    {
        $this->middleware('can:loan-authority-list', ['only' => ['index', 'search']]);
        $this->middleware('can:loan-authority-create', ['only' => ['create']]);
        $this->middleware('can:loan-authority-view', ['only' => ['show']]);
        $this->middleware('can:loan-authority-edit', ['only' => ['update']]);
        $this->middleware('can:loan-authority-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return LoanAuthorityResource::collection(LoanAuthority::latest()->paginate($request->perPage));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreLoanAuthorityRequest $request)
    {
        // save loan authority
       $loanAuthority = LoanAuthority::create([
            'name' => $request->name,
            'email' => $request->email,
            'contact_number' => $request->contactNumber,
            'cc_limit' => $request->ccLoanLimit,
            'address' => clean($request->address),
            'note' => clean($request->note),
            'status' => $request->status,
        ]);

        // add activity log
        activity()
            ->causedBy(Auth::user())
            ->performedOn($loanAuthority)
            ->withProperties([
                'name' => "",
                'code' => '[' . $request->name . ']',
                'event' => 'Create',
                'slug' => $loanAuthority->slug,
                'routeName' => 'authorities.show'
            ])
            ->useLog('Loan Authority Created')
            ->log('Loan Authority Created');

        return $this->responseWithSuccess('Loan authority added successfully');
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
            $loanAuthority = LoanAuthority::with('allLoans.loanAuthority', 'allLoans.loanPayments', 'allLoans.user', 'allLoans.loanTransaction.cashbookAccount')->where('slug', $slug)->first();

            return new AuthorityLoansResource(($loanAuthority));
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
    public function update(UpdateLoanAuthorityRequest $request, $slug)
    {
        $loanAuthority = LoanAuthority::where('slug', $slug)->first();

        try {
            // update loan authority
            $loanAuthority->update([
                'name' => $request->name,
                'email' => $request->email,
                'contact_number' => $request->contactNumber,
                'cc_limit' => $request->ccLoanLimit,
                'address' => clean($request->address),
                'note' => clean($request->note),
                'status' => $request->status,
            ]);

            // add activity log
            activity()
                ->causedBy(Auth::user())
                ->performedOn($loanAuthority)
                ->withProperties([
                    'name' => "",
                    'code' => '[' . $request->name . ']',
                    'event' => 'Update',
                    'slug' => $loanAuthority->slug,
                    'routeName' => 'authorities.show'
                ])
                ->useLog('Loan Authority Updated')
                ->log('Loan Authority Updated');

            return $this->responseWithSuccess('Asset Type updated successfully');
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
            $loanAuthority = LoanAuthority::where('slug', $slug)->first();
            if (($loanAuthority->allLoans) && count($loanAuthority->allLoans) > 0) {
                return $this->responseWithError('Loan authority can\'t be remove');
            }

            // add activity log
            activity()
                ->causedBy(Auth::user())
                ->performedOn($loanAuthority)
                ->withProperties([
                    'name' => "",
                    'code' => '[' . $loanAuthority->name . ']',
                    'event' => 'Delete'
                ])
                ->useLog('Loan Authority Deleted')
                ->log('Loan Authority Deleted');


            $loanAuthority->delete();

            return $this->responseWithSuccess('Loan authority deleted successfully');
        } catch (Exception $e) {
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

        $query = LoanAuthority::where('name', 'LIKE', '%'.$term.'%')
            ->orWhere('slug', 'LIKE', '%'.$term.'%')
            ->orWhere('cc_limit', 'LIKE', '%'.$term.'%')
            ->orWhere('email', 'LIKE', '%'.$term.'%')
            ->orWhere('contact_number', 'LIKE', '%'.$term.'%')
            ->latest()->paginate($request->perPage);

        return LoanAuthorityResource::collection($query);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function allAuthorities()
    {
        $assetTypes = LoanAuthority::where('status', 1)->latest()->get();

        return LoanAuthorityResource::collection($assetTypes);
    }
}
