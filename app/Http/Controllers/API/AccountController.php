<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Account;
use Illuminate\Http\Request;
use App\Services\ImageService;
use App\Models\AccountTransaction;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Http\Resources\AccountResource;
use Intervention\Image\Facades\Image as Image;
use App\Http\Requests\Account\StoreAccountRequest;
use App\Http\Resources\AccountTransactionResource;
use App\Http\Requests\Account\UpdateAccountRequest;

class AccountController extends Controller
{
    private $imageService;
    // define middleware
    public function __construct(ImageService $imageService)
    {
        $this->middleware('can:account-list', ['only' => ['index', 'search']]);
        $this->middleware('can:account-create', ['only' => ['create']]);
        $this->middleware('can:account-view', ['only' => ['show']]);
        $this->middleware('can:account-edit', ['only' => ['update']]);
        $this->middleware('can:account-delete', ['only' => ['destroy']]);

        $this->imageService = $imageService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return AccountResource::collection(Account::latest()->paginate($request->perPage));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAccountRequest $request)
    {
        try {
            // upload thumbnail and set the name
            $imageName = '';
            if ($request->image) {
                $imagePath = public_path('images/accounts/');

                if (!File::exists($imagePath)) {
                    File::makeDirectory($imagePath, 0755, true);
                }
            
                $imageName = time() . '.' . explode('/',explode(':', substr($request->image, 0, strpos($request->image, ';')))[1])[1];
                Image::make($request->image)->save($imagePath . $imageName);
            }

            // store account
            $account = Account::create([
                'bank_name' => $request->bankName,
                'branch_name' => $request->branchName,
                'account_number' => $request->accountNumber,
                'date' => $request->date,
                'image_path' => $imageName,
                'created_by' => auth()->user()->id,
                'note' => clean($request->note),
                'status' => $request->status,
            ]);

            // add activity log
            activity()
                ->causedBy(Auth::user())
                ->performedOn($account)
                ->withProperties([
                    'name' => "",
                    'code' => '[' . $request->accountNumber . ']',
                    'event' => 'Create'
                ])
                ->useLog('Account Created')
                ->log('Account Created');

            return $this->responseWithSuccess('Account added successfully!');
        } catch (Exception $e) {
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
            $account = Account::where('slug', $slug)->with('balanceTransactions.user', 'user')->first();

            return new AccountResource($account);
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
    public function update(UpdateAccountRequest $request, $slug)
    {
        $account = Account::where('slug', $slug)->first();

        try {
            // upload thumbnail and set the name
            $imageName = $account->image_path;
            if ($request->image) {
                $imagePath = public_path('images/accounts/');
            
                // Ensure the directory exists
                if (!File::exists($imagePath)) {
                    File::makeDirectory($imagePath, 0755, true);
                }
            
                // Delete the existing image if it exists
                if (!empty($imageName) && File::exists($imagePath . $imageName)) {
                    File::delete($imagePath . $imageName);
                }
            
                // Generate a new image name and save it
                $imageName = time() . '.' . explode('/',explode(':', substr($request->image, 0, strpos($request->image, ';')))[1])[1];
                Image::make($request->image)->save($imagePath . $imageName);
            }

            // update account
            $account->update([
                'bank_name' => $request->bankName,
                'branch_name' => $request->branchName,
                'account_number' => $request->accountNumber,
                'date' => $request->date,
                'image_path' => $imageName,
                'note' => clean($request->note),
                'status' => $request->status,
            ]);

            // add activity log
            activity()
                ->causedBy(Auth::user())
                ->performedOn($account)
                ->withProperties([
                    'name' => "",
                    'code' => '[' . $request->accountNumber . ']',
                    'event' => 'Update'
                ])
                ->useLog('Account Updated')
                ->log('Account Updated');

            return $this->responseWithSuccess('Account updated successfully');
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
            $account = Account::where('slug', $slug)->first();
            if (isset($account->balanceTransactions) && count($account->balanceTransactions) > 0) {
                return $this->responseWithError('Sorry you can\'t remove this account!');
            } else {
                //delete asset image
                if ($account->image_path) {
                    @unlink(public_path('images/accounts/' . $account->image_path));
                }

                // add activity log
                activity()
                    ->causedBy(Auth::user())
                    ->performedOn($account)
                    ->withProperties([
                        'name' => "",
                        'code' => '[' . $account->account_number . ']',
                        'event' => 'Delete'
                    ])
                    ->useLog('Account Deleted')
                    ->log('Account Deleted');

                $account->delete();

                return $this->responseWithSuccess('Account deleted successfully');
            }
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
        $query = Account::query();

        if ($request->startDate && $request->endDate) {
            $query = $query->whereBetween('date', [$request->startDate, $request->endDate]);
        }

        $query->where(function ($query) use ($term) {
            $query->where('bank_name', 'Like', '%' . $term . '%')
                ->orWhere('branch_name', 'Like', '%' . $term . '%')
                ->orWhere('account_number', 'Like', '%' . $term . '%');
        });

        return AccountResource::collection($query->latest()->paginate($request->perPage));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function allAccounts()
    {
        $accounts = Account::where('status', 1)->latest()->get();

        return AccountResource::collection($accounts);
    }

    // return account transactions
    public function accountTransactions(Request $request, $slug)
    {
        $account = Account::where('slug', $slug)->first();
        $transactions = AccountTransaction::with('user', 'cashbookAccount')->where('account_id', $account->id)->latest()->paginate($request->perPage);

        return AccountTransactionResource::collection($transactions);
    }

    // return search transactions
    public function searchTransactions(Request $request, $slug)
    {
        $term = $request->term;

        $account = Account::where('slug', $slug)->first();
        $transactions = AccountTransaction::with('user', 'cashbookAccount')->where('account_id', $account->id)->where(function ($query) use ($term) {
            $query->orWhere('reason', 'LIKE', '%' . $term . '%')->orWhere('amount', 'LIKE', '%' . $term . '%');
        })->latest()->paginate($request->perPage);

        return AccountTransactionResource::collection($transactions);
    }
}
