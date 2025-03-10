<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Unit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UnitResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Unit\StoreUnitRequest;
use App\Http\Requests\Unit\UpdateUnitRequest;

class UnitController extends Controller
{
    // define middleware
    public function __construct()
    {
        $this->middleware('can:units-management', ['except' => ['allUnits']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return UnitResource::collection(Unit::latest()->paginate($request->perPage));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUnitRequest $request)
    {
        // save unit
       $unit = Unit::create([
            'name' => $request->name,
            'code' => $request->code,
            'note' => $request->note,
            'status' => $request->status,
        ]);

        // add activity log
        activity()
            ->causedBy(Auth::user())
            ->performedOn($unit)
            ->withProperties([
                'name' => "",
                'code' => '[' . $request->name . ']',
                'event' => 'Create'
            ])
            ->useLog('Unit Created')
            ->log('Unit Created');

        return $this->responseWithSuccess('Unit added successfully');
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
            $unit = Unit::where('slug', $slug)->first();

            return new UnitResource($unit);
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
    public function update(UpdateUnitRequest $request, $slug)
    {
        $unit = Unit::where('slug', $slug)->first();

        try {
            // update unit
            $unit->update([
                'name' => $request->name,
                'code' => $request->code,
                'note' => $request->note,
                'status' => $request->status,
            ]);

            // add activity log
            activity()
                ->causedBy(Auth::user())
                ->performedOn($unit)
                ->withProperties([
                    'name' => "",
                    'code' => '[' . $request->name . ']',
                    'event' => 'Update'
                ])
                ->useLog('Unit Updated')
                ->log('Unit Updated');

            return $this->responseWithSuccess('Unit updated successfully');
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
            $unit = Unit::where('slug', $slug)->first();

            // add activity log
            activity()
                ->causedBy(Auth::user())
                ->performedOn($unit)
                ->withProperties([
                    'name' => "",
                    'code' => '[' . $unit->name . ']',
                    'event' => 'Delete'
                ])
                ->useLog('Unit Deleted')
                ->log('Unit Deleted');


            $unit->delete();

            return $this->responseWithSuccess('Unit deleted successfully');
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

        $query = Unit::where('name', 'LIKE', '%'.$term.'%')
            ->orWhere('code', 'LIKE', '%'.$term.'%')
            ->orWhere('note', 'LIKE', '%'.$term.'%')
            ->latest()->paginate($request->perPage);

        return UnitResource::collection($query);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function allUnits()
    {
        $units = Unit::where('status', 1)->latest()->get();

        return UnitResource::collection($units);
    }
}
