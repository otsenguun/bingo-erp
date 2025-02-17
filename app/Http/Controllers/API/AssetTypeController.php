<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\AssetType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\AssetTypeResource;
use App\Http\Requests\AssetType\StoreAssetTypeRequest;
use App\Http\Requests\AssetType\UpdateAssetTypeRequest;

class AssetTypeController extends Controller
{
    // define middleware
    public function __construct()
    {
        $this->middleware('can:asset-type-list', ['only' => ['index', 'search']]);
        $this->middleware('can:asset-type-create', ['only' => ['create']]);
        $this->middleware('can:asset-type-edit', ['only' => ['update']]);
        $this->middleware('can:asset-type-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return AssetTypeResource::collection(AssetType::latest()->paginate($request->perPage));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAssetTypeRequest $request)
    {
        // save asset type
       $assetType = AssetType::create([
            'name' => $request->name,
            'note' => clean($request->note),
            'status' => $request->status,
        ]);

        // add activity log
        activity()
        ->causedBy(Auth::user())
        ->performedOn($assetType)
        ->withProperties([
            'name' => "",
            'code' => '[' . $request->name . ']',
            'event' => 'Create',
            'slug' => $assetType->slug,
            'routeName' => ''
        ])
        ->useLog('Asset Type Created')
        ->log('Asset Type Created');

        return $this->responseWithSuccess('Asset type added successfully');
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
            $assetType = AssetType::where('slug', $slug)->first();

            return $assetType;
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
    public function update(UpdateAssetTypeRequest $request, $slug)
    {
        $assetType = AssetType::where('slug', $slug)->first();

        try {
            // update asset type
            $assetType->update([
                'name' => $request->name,
                'note' => clean($request->note),
                'status' => $request->status,
            ]);

            // add activity log
            activity()
            ->causedBy(Auth::user())
            ->performedOn($assetType)
            ->withProperties([
                'name' => "",
                'code' => '[' . $request->name . ']',
                'event' => 'Update',
                'slug' => $assetType->slug,
                'routeName' => ''
            ])
            ->useLog('Asset Type Updated')
            ->log('Asset Type Updated');

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
            $assetType = AssetType::where('slug', $slug)->first();

            activity()
            ->causedBy(Auth::user())
            ->performedOn($assetType)
            ->withProperties([
                'name' => "",
                'code' => '[' . $assetType->name . ']',
                'event' => 'Delete'
            ])
            ->useLog('Asset Type Deleted')
            ->log('Asset Type Deleted');


            $assetType->delete();

            return $this->responseWithSuccess('Asset type deleted successfully');
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

        $query = AssetType::where('name', 'LIKE', '%'.$term.'%')
            ->orWhere('slug', 'LIKE', '%'.$term.'%')
            ->orWhere('note', 'LIKE', '%'.$term.'%')
            ->latest()->paginate($request->perPage);

        return AssetTypeResource::collection($query);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function allAssets()
    {
        $assetTypes = AssetType::where('status', 1)->latest()->get();

        return AssetTypeResource::collection($assetTypes);
    }
}
