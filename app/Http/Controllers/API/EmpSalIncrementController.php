<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\SalaryIncrement;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\SalaryIncrementResource;
use App\Http\Requests\Employee\StoreSalarayIncrementRequest;

class EmpSalIncrementController extends Controller
{
    // define middleware
    public function __construct()
    {
        $this->middleware('can:increment-list', ['only' => ['index', 'search']]);
        $this->middleware('can:increment-create', ['only' => ['create']]);
        $this->middleware('can:increment-view', ['only' => ['show']]);
        $this->middleware('can:increment-edit', ['only' => ['update']]);
        $this->middleware('can:increment-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return SalaryIncrementResource::collection(SalaryIncrement::with('employee.department')->latest()->paginate($request->perPage));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSalarayIncrementRequest $request)
    {
        // get employee
        if (! empty($request->employee)) {
            $employee = Employee::where('slug', $request->employee['slug'])->first();
        }

        try {
            DB::beginTransaction();

            // create increment
          $SalaryIncrement =  SalaryIncrement::create([
                'reason' => $request->reason,
                'empolyee_id' => $employee->id,
                'increment_amount' => $request->incrementAmount,
                'increment_date' => $request->incrementDate ? $request->incrementDate : date('Y-m-d'),
                'note' => $request->note,
                'status' => $request->status,
                'created_by' => auth()->user()->id,
            ]);

            // add activity log
            activity()
                ->causedBy(Auth::user())
                ->performedOn($SalaryIncrement)
                ->withProperties([
                    'name' => "",
                    'code' => '[' . $employee->name . ']',
                    'event' => 'Create',
                    'slug' => $SalaryIncrement->slug,
                    'routeName' => 'increments.show'
                ])
                ->useLog('Employee Salary Increment Created')
                ->log('Employee Salary Increment Created');

            DB::commit();

            return $this->responseWithSuccess('Increment added successfully');
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
            $increment = SalaryIncrement::with('employee.department', 'user')->where('slug', $slug)->first();

            return new SalaryIncrementResource($increment);
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
    public function update(StoreSalarayIncrementRequest $request, $slug)
    {
        // get employee
        if (! empty($request->employee)) {
            $employee = Employee::where('slug', $request->employee['slug'])->first();
        }

        try {
            DB::beginTransaction();

            $increment = SalaryIncrement::with('employee.department')->where('slug', $slug)->first();
            // update increment
            $increment->update([
                'reason' => $request->reason,
                'empolyee_id' => $employee->id,
                'increment_amount' => $request->incrementAmount,
                'increment_date' => $request->incrementDate,
                'note' => $request->note,
                'status' => $request->status,
            ]);

            // add activity log
            activity()
                ->causedBy(Auth::user())
                ->performedOn($increment)
                ->withProperties([
                    'name' => "",
                    'code' => '[' . $employee->name . ']',
                    'event' => 'Updated',
                    'slug' => $increment->slug,
                    'routeName' => 'increments.show'
                ])
                ->useLog('Employee Salary Increment Updated')
                ->log('Employee Salary Increment Updated');

            DB::commit();

            return $this->responseWithSuccess('Increment updated successfully');
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

            $increment = SalaryIncrement::where('slug', $slug)->first();

            // add activity log
            activity()
                ->causedBy(Auth::user())
                ->performedOn($increment)
                ->withProperties([
                    'name' => "",
                    'code' => '[' . $increment->employee->name . ']',
                    'event' => 'Delete'
                ])
                ->useLog('Employee Salary Increment Deleted')
                ->log('Employee Salary Increment Deleted');

            $increment->delete();

            DB::commit();

            return $this->responseWithSuccess('Increment deleted successfully');
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
        $query = SalaryIncrement::query();

        if ($request->startDate && $request->endDate) {
            $query = $query->whereBetween('increment_date', [$request->startDate, $request->endDate]);
        }

        $query->where(function ($query) use ($term) {
            $query->where('reason', 'LIKE', '%'.$term.'%')
                ->orWhere('increment_amount', 'LIKE', '%'.$term.'%')
                ->orWhereHas('employee', function ($newQuery) use ($term) {
                    $newQuery->where('name', 'LIKE', '%'.$term.'%')
                        ->orWhere('name', 'LIKE', '%'.$term.'%')
                        ->orWhere('emp_id', 'LIKE', '%'.$term.'%')
                        ->orWhere('designation', 'LIKE', '%'.$term.'%')
                        ->orWhere('salary', 'LIKE', '%'.$term.'%');
                });
        });

        return SalaryIncrementResource::collection($query->latest()->paginate($request->perPage));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function allIncrements()
    {
        $allIncrements = SalaryIncrement::latest()->get();

        return SalaryIncrementResource::collection($allIncrements);
    }
}
