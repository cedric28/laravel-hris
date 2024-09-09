<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Log;
use App\Payroll;
use Carbon\Carbon;
use Validator;
use Illuminate\Validation\Rule;

class PayrollController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $payrolls = Payroll::all();
        $inActivePayrolls = Payroll::onlyTrashed()->get();

        return view("payroll-setting.index", [
            'payrolls' => $payrolls,
            'inActivePayrolls' => $inActivePayrolls
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //prevent other user to access to this page
        $this->authorize("isHROrAdmin");
        return view("payroll-setting.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Prevent other users from accessing this page
        $this->authorize("isHROrAdmin");
    
        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();
    
        try {
            // Parse start_date and end_date from request
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
    
            // Validate the basic fields
            $validator = Validator::make($request->all(), [
                'description' => [
                    'required',
                    'string',
                    'max:255'
                ],
                'start_date' => 'required|date',
                'end_date' => 'required|date',
            ]);
    
            // Custom validation for payroll dates
            $validator->after(function ($validator) use ($startDate, $endDate) {

                 // Ensure the start date is before or equal to the end date
                if ($startDate->gt($endDate)) {
                    $validator->errors()->add('start_date', 'The start date must be earlier than or equal to the end date.');
                }
                 // Ensure the date range is exactly 15 days
                if ($startDate->diffInDays($endDate) !== 14) { // 14 days between to make it 15 total
                    $validator->errors()->add('end_date', 'The end date must be exactly 15 days after the start date.');
                }

                // Ensure both dates are within the same month
                if ($startDate->month !== $endDate->month) {
                    $validator->errors()->add('end_date', 'The start and end dates must be within the same month.');
                }
    
                // Check for overlapping dates in existing payroll records
                $conflictingPayroll = Payroll::where(function($query) use ($startDate, $endDate) {
                    $query->whereBetween('start_date', [$startDate, $endDate])
                          ->orWhereBetween('end_date', [$startDate, $endDate])
                          ->orWhere(function ($query) use ($startDate, $endDate) {
                              $query->where('start_date', '<=', $startDate)
                                    ->where('end_date', '>=', $endDate);
                          });
                })->exists();
    
                if ($conflictingPayroll) {
                    $validator->errors()->add('start_date', 'The selected date range conflicts with an existing payroll.');
                    $validator->errors()->add('end_date', 'The selected date range conflicts with an existing payroll.');
                }
            });
    
            // If validation fails, return with errors
            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }
    
            // Check current user
            $user = \Auth::user()->id;
    
            // Save payroll data
            $payroll = new Payroll();
            $payroll->description = $request->description;
            $payroll->start_date = $startDate->format('Y-m-d');
            $payroll->end_date = $endDate->format('Y-m-d');
            $payroll->creator_id = $user;
            $payroll->updater_id = $user;
            $payroll->save();
    
            // Log creation
            $log = new Log();
            $log->log = "User " . \Auth::user()->email . " created payroll cutoff " . $payroll->id . " at " . Carbon::now();
            $log->creator_id = \Auth::user()->id;
            $log->updater_id = \Auth::user()->id;
            $log->save();
    
            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();
    
            return redirect()->route('payroll.create')
                ->with('successMsg', 'Payroll Setting Cut Off Save Successful');
        } catch (\Exception $e) {
            // If error occurs, rollback the data
            \DB::rollback();
            return back()->withErrors($e->getMessage());
        }
    }
    

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
          //prevent other user to access to this page
          $this->authorize("isHROrAdmin");
          $payroll = Payroll::withTrashed()->findOrFail($id);
          $startDate = Carbon::parse($payroll->start_date)->format('m/d/y');
          $endDate = Carbon::parse($payroll->end_date)->format('m/d/y');
          return view('payroll-setting.edit', [
              'payroll' => $payroll,
              'startDate' => $startDate,
              'endDate' => $endDate
          ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Prevent unauthorized access
        $this->authorize("isHROrAdmin");
    
        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();
    
        try {
            // Find the existing payroll record
            $payroll = Payroll::findOrFail($id);
    
            // Validate the basic fields
            $validator = Validator::make($request->all(), [
                'description' => [
                    'required',
                    'string',
                    'max:255'
                ],
                'start_date' => 'required|date',
                'end_date' => 'required|date',
            ]);
    
            // Only run the following validations if the user is trying to update the dates
            if ($request->has('start_date') && $request->has('end_date')) {
                // Parse start_date and end_date from request
                $startDate = Carbon::parse($request->start_date);
                $endDate = Carbon::parse($request->end_date);
    
                // Custom validation for payroll dates
                $validator->after(function ($validator) use ($startDate, $endDate, $id) {
                    // Ensure the start date is before or equal to the end date
                    if ($startDate->gt($endDate)) {
                        $validator->errors()->add('start_date', 'The start date must be earlier than or equal to the end date.');
                    }
    
                    // Ensure the date range is exactly 15 days
                    if ($startDate->diffInDays($endDate) !== 14) { // 14 days between to make it 15 total
                        $validator->errors()->add('end_date', 'The end date must be exactly 15 days after the start date.');
                    }
    
                    // Ensure both dates are within the same month
                    if ($startDate->month !== $endDate->month) {
                        $validator->errors()->add('end_date', 'The start and end dates must be within the same month.');
                    }
    
                    // Check for overlapping dates in existing payroll records, excluding the current record
                    $conflictingPayroll = Payroll::where('id', '!=', $id)
                        ->where(function($query) use ($startDate, $endDate) {
                            $query->whereBetween('start_date', [$startDate, $endDate])
                                  ->orWhereBetween('end_date', [$startDate, $endDate])
                                  ->orWhere(function ($query) use ($startDate, $endDate) {
                                      $query->where('start_date', '<=', $startDate)
                                            ->where('end_date', '>=', $endDate);
                                  });
                        })->exists();
    
                    if ($conflictingPayroll) {
                        $validator->errors()->add('start_date', 'The selected date range conflicts with an existing payroll.');
                        $validator->errors()->add('end_date', 'The selected date range conflicts with an existing payroll.');
                    }
                });
            }
    
            // If validation fails, return with errors
            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }
    
            // Check current user
            $user = \Auth::user()->id;
    
            // Update only the fields that were provided
            $payroll->description = $request->description;
    
            // Only update the dates if they are provided in the request
            if ($request->has('start_date') && $request->has('end_date')) {
                $payroll->start_date = $startDate->format('Y-m-d');
                $payroll->end_date = $endDate->format('Y-m-d');
            }
    
            $payroll->updater_id = $user;
            $payroll->save();
    
            // Log update
            $log = new Log();
            $log->log = "User " . \Auth::user()->email . " updated payroll cutoff " . $payroll->id . " at " . Carbon::now();
            $log->creator_id = \Auth::user()->id;
            $log->updater_id = \Auth::user()->id;
            $log->save();
    
            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();
    
            return redirect()->route('payroll.index')
                ->with('successMsg', 'Payroll updated successfully');
        } catch (\Exception $e) {
            // If error occurs, rollback the data
            \DB::rollback();
            return back()->withErrors($e->getMessage());
        }
    }
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //prevent other user to access to this page
        $this->authorize("isHROrAdmin");

        //delete category
        $payroll = Payroll::findOrFail($id);
        $payroll->delete();

        $log = new Log();
        $log->log = "User " . \Auth::user()->email . " delete payroll " . $payroll->id . " at " . Carbon::now();
        $log->creator_id =  \Auth::user()->id;
        $log->updater_id =  \Auth::user()->id;
        $log->save();
    }


     /**
     * Restore the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        \DB::beginTransaction();
        try {

            $payroll = Payroll::onlyTrashed()->findOrFail($id);

            /* Restore payroll */
            $payroll->restore();

            $log = new Log();
            $log->log = "User " . \Auth::user()->email . " restore payroll " . $payroll->id . " at " . Carbon::now();
            $log->creator_id =  \Auth::user()->id;
            $log->updater_id =  \Auth::user()->id;
            $log->save();

            \DB::commit();
            return back()->with("successMsg", "Successfully Restore the data");
        } catch (\Exception $e) {
            \DB::rollback();
            return back()->withErrors($e->getMessage());
        }
    }
}
