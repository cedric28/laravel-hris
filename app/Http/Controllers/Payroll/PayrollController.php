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
        $imagePath = public_path('assets/img/logo.png');
        $base64Logo = 'data:image/png;base64,' . base64_encode(file_get_contents($imagePath));
        $currentUser = \Auth::user()->first_name . ' ' . \Auth::user()->last_name;
        return view("payroll-setting.index", [
            'payrolls' => $payrolls,
            'inActivePayrolls' => $inActivePayrolls,
            'base64Logo'=> $base64Logo,
            'currentUser' => $currentUser
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
        $this->authorize("isHROrAdmin");
        \DB::beginTransaction();

        try {
            $userId = \Auth::id();
            $mode = $request->mode;

            if ($mode === 'manual') {
                $request->validate([
                    'description' => 'required|string|max:255',
                    'start_date' => 'required|date',
                    'end_date' => 'required|date',
                ]);

                $startDate = Carbon::parse($request->start_date);
                $endDate = Carbon::parse($request->end_date);

                // Validate 15-day range
                if ($startDate->gt($endDate) || $startDate->diffInDays($endDate) !== 14 || $startDate->month !== $endDate->month) {
                    return back()->withErrors('Invalid date range. Ensure dates are 15 days apart and in the same month.')->withInput();
                }

                // Overlap check
                if ($this->isOverlapping($startDate, $endDate)) {
                    return back()->withErrors('Selected date range overlaps with an existing payroll.')->withInput();
                }

                Payroll::create([
                    'description' => $request->description,
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                    'creator_id' => $userId,
                    'updater_id' => $userId,
                ]);

            } elseif ($mode === 'auto') {
                $request->validate(['year' => 'required|integer|min:2000']);

                $year = (int) $request->year;
                $cutoffs = [];

                for ($month = 1; $month <= 12; $month++) {
                    $start1 = Carbon::create($year, $month, 1);
                    $end1 = Carbon::create($year, $month, 15);
                    $start2 = Carbon::create($year, $month, 16);
                    $end2 = Carbon::create($year, $month, $start2->daysInMonth);

                    $cutoffs[] = [
                        'desc' => $start1->format('F') . ' 15th Payroll',
                        'start' => $start1,
                        'end' => $end1
                    ];
                    $cutoffs[] = [
                        'desc' => $start2->format('F') . ' End of Month Payroll',
                        'start' => $start2,
                        'end' => $end2
                    ];
                }

                foreach ($cutoffs as $c) {
                    if (!$this->isOverlapping($c['start'], $c['end'])) {
                        Payroll::create([
                            'description' => $c['desc'],
                            'start_date' => $c['start']->format('Y-m-d'),
                            'end_date' => $c['end']->format('Y-m-d'),
                            'creator_id' => $userId,
                            'updater_id' => $userId,
                        ]);
                    }
                }
            }

            Log::create([
                'log' => "User " . \Auth::user()->email . " created payroll(s)",
                'creator_id' => $userId,
                'updater_id' => $userId,
            ]);

            \DB::commit();
            return redirect()->route('payroll.create')->with('successMsg', 'Payroll saved successfully.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->withErrors('Error: ' . $e->getMessage())->withInput();
        }
    }

// Helper
private function isOverlapping($start, $end)
{
    return Payroll::where(function($q) use ($start, $end) {
        $q->whereBetween('start_date', [$start, $end])
          ->orWhereBetween('end_date', [$start, $end])
          ->orWhere(function ($query) use ($start, $end) {
              $query->where('start_date', '<=', $start)
                    ->where('end_date', '>=', $end);
          });
    })->exists();
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
