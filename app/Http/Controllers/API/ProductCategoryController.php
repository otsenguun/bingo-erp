<?php

namespace App\Http\Controllers\API;

use Exception;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ProductCategoryResource;
use App\Http\Requests\Product\StoreProductCategoryRequest;
use App\Http\Requests\Product\UpdateProductCategoryRequest;

class ProductCategoryController extends Controller
{
    // define middleware
    public function __construct()
    {
        $this->middleware('can:product-category-list', ['only' => ['index', 'search']]);
        $this->middleware('can:product-category-create', ['only' => ['create']]);
        $this->middleware('can:product-category-edit', ['only' => ['update']]);
        $this->middleware('can:product-category-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return ProductCategoryResource::collection(ProductCategory::latest()->paginate($request->perPage));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProductCategoryRequest $request)
    {
        try {
            // generate code
            $code = 1;
            $prevCode = ProductCategory::latest()->first();
            if ($prevCode) {
                $code = $prevCode->code + 1;
            }

            // save category
          $ProductCategory =  ProductCategory::create([
                'name' => $request->name,
                'code' => $code,
                'note' => $request->note,
                'status' => $request->status,
            ]);

            // add activity log
            activity()
                ->causedBy(Auth::user())
                ->performedOn($ProductCategory)
                ->withProperties([
                    'name' => $request->name,
                    'code' => '[' . config('config.proCatPrefix') . '-' . $code . ']',
                    'event' => 'Create',
                    'slug' => $ProductCategory->slug,
                    'routeName' => ''
                ])
                ->useLog('Product Category Created')
                ->log('Product Category Created');

            return $this->responseWithSuccess('Category added successfully');
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
            $category = ProductCategory::where('slug', $slug)->first();

            return $category;
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
    public function update(UpdateProductCategoryRequest $request, $slug)
    {
        $category = ProductCategory::where('slug', $slug)->first();

        try {
            // update category
            $category->update([
                'name' => $request->name,
                'note' => $request->note,
                'status' => $request->status,
            ]);

            // add activity log
            activity()
                ->causedBy(Auth::user())
                ->performedOn($category)
                ->withProperties([
                    'name' => $category->name,
                    'code' => '[' . config('config.proCatPrefix') . '-' . $category->code . ']',
                    'event' => 'Update',
                    'slug' => $category->slug,
                    'routeName' => ''
                ])
                ->useLog('Product Category Updated')
                ->log('Product Category Updated');

            return $this->responseWithSuccess('Category updated successfully');
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
            $category = ProductCategory::where('slug', $slug)->first();

            // add activity log
            activity()
                ->causedBy(Auth::user())
                ->performedOn($category)
                ->withProperties([
                    'name' => $category->name,
                    'code' => '[' . config('config.proCatPrefix') . '-' . $category->code . ']',
                    'event' => 'Delete'
                ])
                ->useLog('Product Category Deleted')
                ->log('Product Category Deleted');


            $category->delete();

            return $this->responseWithSuccess('Category deleted successfully');
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

        $query = ProductCategory::where('name', 'LIKE', '%'.$term.'%')
            ->orWhere('slug', 'LIKE', '%'.$term.'%')
            ->orWhere('note', 'LIKE', '%'.$term.'%')
            ->latest()->paginate($request->perPage);

        return ProductCategoryResource::collection($query);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function allCategories()
    {
        $categories = ProductCategory::where('status', 1)->latest()->get();

        return ProductCategoryResource::collection($categories);
    }
}
