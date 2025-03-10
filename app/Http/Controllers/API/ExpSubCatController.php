<?php

namespace App\Http\Controllers\API;

use Exception;
use Illuminate\Http\Request;
use App\Models\ExpenseCategory;
use App\Models\ExpenseSubCategory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ExpenseSubCategoryResource;
use App\Http\Requests\Expense\StoreExpenseSubCategoryRequest;
use App\Http\Requests\Expense\UpdateExpenseSubCategoryRequest;

class ExpSubCatController extends Controller
{
    // define middleware
    public function __construct()
    {
        $this->middleware('can:expense-sub-category-list', ['only' => ['index', 'search']]);
        $this->middleware('can:expense-sub-category-create', ['only' => ['store']]);
        $this->middleware('can:expense-sub-category-edit', ['only' => ['update']]);
        $this->middleware('can:expense-sub-category-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return ExpenseSubCategoryResource::collection(ExpenseSubCategory::with('expCategory')->latest()->paginate($request->perPage));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreExpenseSubCategoryRequest $request)
    {
        try {
            // generate code
            $code = 1;
            $prevCode = ExpenseSubCategory::latest()->first();
            if ($prevCode) {
                $code = $prevCode->code + 1;
            }

            // create sub category
           $subCategory = ExpenseSubCategory::create([
                'name' => $request->name,
                'code' => $code,
                'exp_id' => $request->category['id'],
                'note' => clean($request->note),
                'status' => $request->status,
            ]);

            // add activity log
            activity()
            ->performedOn($subCategory)
            ->causedBy(Auth::user())
            ->withProperties([
                'name' => $request->name,
                'code' => '[' . config('config.expSubCatPrefix') . '-' . $code . ']',
                'event' => 'Create',
                'slug' => $subCategory->slug,
                'routeName' => ''
            ])
            ->useLog('Expenses Sub Category Created')
            ->log('Expenses Sub Category Created');

            return $this->responseWithSuccess('Sub category added successfully');
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
            $subCategory = ExpenseSubCategory::with('expCategory')->where('slug', $slug)->first();

            return new ExpenseSubCategoryResource($subCategory);
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
    public function update(UpdateExpenseSubCategoryRequest $request, $slug)
    {
        $subCategory = ExpenseSubCategory::where('slug', $slug)->first();

        try {
            // update sub category
            $subCategory->update([
                'name' => $request->name,
                'exp_id' => $request->category['id'],
                'note' => clean($request->note),
                'status' => $request->status,
            ]);

                  // add activity log
            activity()
            ->causedBy(Auth::user())
            ->performedOn($subCategory)
            ->withProperties([
                'name' => $subCategory->name,
                'code' => '[' . config('config.expSubCatPrefix') . '-' . $subCategory->code . ']',
                'event' => 'Update',
                'slug' => $subCategory->slug,
                'routeName' => ''
            ])
            ->useLog('Expenses Sub Category Updated')
            ->log('Expenses Sub Category Updated');

            return $this->responseWithSuccess('Sub category updated successfully');
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
            $subCategory = ExpenseSubCategory::where('slug', $slug)->first();


            // add activity log
            activity()
            ->causedBy(Auth::user())
            ->performedOn($subCategory)
            ->withProperties([
                'name' => $subCategory->name,
                'code' => '[' . config('config.expSubCatPrefix') . '-' . $subCategory->code . ']',
                'event' => 'Delete'
            ])
            ->useLog('Expenses Sub Category Deleted')
            ->log('Expenses Sub Category Deleted');

            $subCategory->allExpenses->each->delete();
            $subCategory->delete();

            return $this->responseWithSuccess('Sub category deleted successfully');
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

        $query = ExpenseSubCategory::with('expCategory')->where('name', 'LIKE', '%'.$term.'%')
            ->orWhere('code', 'LIKE', '%'.$term.'%')
            ->orWhere('note', 'LIKE', '%'.$term.'%')
            ->orWhereHas('expCategory', function ($newQuery) use ($term) {
                $newQuery->where('name', 'LIKE', '%'.$term.'%')
                    ->orWhere('code', 'LIKE', '%'.$term.'%');
            });

        return ExpenseSubCategoryResource::collection($query->latest()->paginate($request->perPage));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function allSubCategories()
    {
        $subCategories = ExpenseSubCategory::with('expCategory')->where('status', 1)->latest()->get();

        return ExpenseSubCategoryResource::collection($subCategories);
    }

    // retun subcategories by category
    public function subCategoriesByCategory($slug)
    {
        $category = ExpenseCategory::where('slug', $slug)->first();
        $subCategories = ExpenseSubCategory::with('expCategory')->where('exp_id', $category->id)->latest()->get();

        return ExpenseSubCategoryResource::collection($subCategories);
    }
}
