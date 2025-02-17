<?php

namespace App\Http\Controllers\Central;

use App\Models\Feature;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Feature\FeatureResource;
use App\Http\Requests\Feature\StoreFeatureRequest;
use App\Http\Requests\Feature\UpdateFeatureRequest;

class FeatureController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        return FeatureResource::collection(Feature::latest()->paginate($request->perPage));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Feature\StoreFeatureRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreFeatureRequest $request)
    {
        $feature = Feature::create($request->validated());

        // add activity log
        activity()
        ->causedBy(Auth::user())
        ->performedOn($feature)
        ->withProperties([
            'name' => $request->name,
            'code' =>  '[' . $request->name . ']',
            'event' => 'Create',
            'slug' => '/',
            'routeName' => 'features.index'
        ])
        ->useLog('Feature Created')
        ->log('Feature Created');

        return $this->responseWithSuccess('Feature uploaded successfully!', new FeatureResource($feature));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Feature  $feature
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Feature $feature)
    {
        return $this->responseWithSuccess('Feature retrieved successfully', new FeatureResource($feature));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Feature\UpdateFeatureRequest  $request
     * @param  \App\Models\Feature  $feature
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateFeatureRequest $request, Feature $feature)
    {
        $feature->update($request->validated());

        // add activity log
        activity()
        ->causedBy(Auth::user())
        ->performedOn($feature)
        ->withProperties([
            'name' => $request->name,
            'code' =>  '[' . $request->name . ']',
            'event' => 'Update',
            'slug' => '/',
            'routeName' => 'features.index'
        ])
        ->useLog('Feature Updated')
        ->log('Feature Updated');

        return $this->responseWithSuccess('Feature updated successfully!', new FeatureResource($feature));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Feature  $feature
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Feature $feature)
    {
         // add activity log
         activity()
         ->causedBy(Auth::user())
         ->performedOn($feature)
         ->withProperties([
             'name' => $feature->name,
             'code' =>  '[' . $feature->name . ']',
             'event' => 'Delete',
             'slug' => '/',
             'routeName' => ''
         ])
         ->useLog('Feature Deleted')
         ->log('Feature Deleted');

        $feature->delete();

        return $this->responseWithSuccess('Feature deleted successfully!');
    }

    /**
     * search resource from storage.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function search(Request $request)
    {
        $term = $request->term;
        $query = Feature::query();

        $query->where(function ($query) use ($term) {
            $query->where('name', 'Like', '%' . $term . '%');
        });

        return FeatureResource::collection($query->latest()->paginate($request->perPage));
    }
}