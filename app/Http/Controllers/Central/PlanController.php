<?php

namespace App\Http\Controllers\Central;

use App\Models\Plan;
use Illuminate\Http\Request;
use App\Services\ImageService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Plan\PlanResource;
use App\Http\Requests\Plan\StorePlanRequest;
use App\Http\Requests\Plan\UpdatePlanRequest;

class PlanController extends Controller
{
    public function index(Request $request)
    {
        return PlanResource::collection(Plan::paginate($request->perPage));
    }

    public function store(StorePlanRequest $request, ImageService $imageService)
    {
        $imageNameWithStringFolderPath = $imageService->uploadImageAndGetPath($request->image, 'plans');
        $savedPlan = Plan::create([
            'image' => $imageNameWithStringFolderPath,
        ] + $request->safe()->except('features'));

        $savedPlan->features()->sync($request->validated('features'));

        // add activity log
        activity()
        ->causedBy(Auth::user())
        ->performedOn($savedPlan)
        ->withProperties([
            'name' => $request->name,
            'code' =>  '[' . $request->name . ']',
            'event' => 'Create',
            'slug' => '/',
            'routeName' => 'plans.index'
        ])
        ->useLog('Plan Created')
        ->log('Plan Created');

        return new PlanResource($savedPlan);
    }

    public function show(Plan $plan)
    {
        return new PlanResource($plan);
    }

    /**
     * @throws \Exception
     */
    public function update(UpdatePlanRequest $request, Plan $plan, ImageService $imageService)
    {
        // if image not changed, then do not update image
        if (filter_var($request->image, FILTER_VALIDATE_URL)) {
            $plan->update($request->safe()->except('features'));
            $plan->features()->sync($request->validated('features'));

            // add activity log
            activity()
            ->causedBy(Auth::user())
            ->performedOn($plan)
            ->withProperties([
                'name' => $request->name,
                'code' =>  '[' . $request->name . ']',
                'event' => 'Update',
                'slug' => '/',
                'routeName' => 'plans.index'
            ])
            ->useLog('Plan Updated')
            ->log('Plan Updated');

            return $this->responseWithSuccess('Plan updated successfully!');
        }

        // if image updated
        $imageService->validateBase64Image($request->image);

        $imageNameWithStringFolderPath = $imageService->uploadImageAndGetPath($request->image, 'plans');

        // update the plan name in database
        $plan->update($request->safe()->except(['image', 'features']) + [
            'image' => $imageNameWithStringFolderPath,
        ]);

        $plan->features()->sync($request->validated('features'));

        // add activity log
        activity()
        ->causedBy(Auth::user())
        ->performedOn($plan)
        ->withProperties([
            'name' => $request->name,
            'code' =>  '[' . $request->name . ']',
            'event' => 'Update',
            'slug' => '/',
            'routeName' => 'plans.index'
        ])
        ->useLog('Plan Updated')
        ->log('Plan Updated');

        return new PlanResource($plan);
    }

    public function destroy(Plan $plan)
    {
        try {
            if (!$plan->tenants()->exists()) {

                // add activity log
                activity()
                ->causedBy(Auth::user())
                ->performedOn($plan)
                ->withProperties([
                    'name' => $plan->name,
                    'code' =>  '[' . $plan->name . ']',
                    'event' => 'Delete',
                    'slug' => '/',
                    'routeName' => ''
                ])
                ->useLog('Plan Deleted')
                ->log('Plan Deleted');

                $plan->delete();

                return $this->responseWithSuccess($plan->name . ' deleted successfully');
            }

            return $this->responseWithError('This plan is associated with a tenant. You can not delete it.');
        } catch (\Exception $exception) {
            return $this->responseWithError($exception->getMessage());
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
        $query = Plan::query();

        $query->where(function ($query) use ($term) {
            $query->where('name', 'Like', '%' . $term . '%')
                ->orWhere('description', 'Like', '%' . $term . '%')
                ->orWhere('currency', 'Like', '%' . $term . '%')
                ->orWhere('interval', 'Like', '%' . $term . '%');
        });

        return PlanResource::collection($query->latest()->paginate($request->perPage));
    }
}