<?php

namespace App\Http\Controllers\API;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;
use App\Http\Resources\ActivityLogResource;

class ActivityLogController extends Controller
{

    public function index(Request $request)
    {
        return ActivityLogResource::collection(Activity::latest()->paginate($request->perPage));
    }

    public function search(Request $request)
    {
        $term = $request->term;
        $query = Activity::query();

        if ($request->startDate && $request->endDate) {
            $query->whereBetween('created_at', [$request->startDate, $request->endDate]);
        }

        $query->where(function ($q) use ($term) {
            $q->where('log_name', 'LIKE', '%' . $term . '%')
                ->orWhere('description', 'LIKE', '%' . $term . '%')
                ->orWhere('event', 'LIKE', '%' . $term . '%');
        });

        $results = $query->latest()->paginate($request->perPage);

        return ActivityLogResource::collection($results);
    }

    public function specific(Request $request)
    {
        $expenseSlug = $request->slug;
        $modelName = 'App\\Models\\' . $request->input('modelName');
        $model = $modelName;
        $modelInstance = new $model;

        $data = $modelInstance::where('slug', $expenseSlug)->first();
        $query = Activity::where('subject_id', optional($data)->id)
            ->where('subject_type', $model);

        // Check if search term is provided
        if ($request->has('term')) {
            $term = $request->input('term');
            $query->where(function ($query) use ($term) {
                $query->where('log_name', 'LIKE', '%' . $term . '%')
                    ->orWhere('description', 'LIKE', '%' . $term . '%')
                    ->orWhere('event', 'LIKE', '%' . $term . '%');
            });
        }

        return ActivityLogResource::collection($query->latest()->paginate($request->perPage));
    }

    public function delete(Request $request)
    {
        try {
            $id = $request->id;
            $activity = Activity::where('id', $id)->first();
            $activity->delete();

            return response()->json(['success' => true, 'message' => 'Activity deleted successfully']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
