<?php

namespace App\Http\Controllers\OverTime;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Imports\OverTimeImport;
use App\Deployment;
use App\Log;
use App\OverTime;
use App\Attendance;
use Validator, Hash, DB;
use Carbon\Carbon;

class OverTimeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("overtime.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         //prevent other user to access to this page
         $this->authorize("isHROrAdmin");

         /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();

        try {
            $deployment = Deployment::withTrashed()->findOrFail($request->deployment_id);
          
            $rules = [
              'overtime_date' => [
                  'required',
                  'string',
                  function ($attribute, $value, $fail) use ($request) {
                    $attendance = Attendance::where('attendance_date',Carbon::parse($value)->format('Y-m-d'))
                      ->where('deployment_id', $request->deployment_id)
                      ->where('status', 'Present')
                      ->exists();

                    $overtime = OverTime::where('overtime_date',Carbon::parse($value)->format('Y-m-d'))
                    ->where('deployment_id', $request->deployment_id)
                    ->exists();

                    if($attendance) {
                        if ($overtime) {
                            $fail('Overtime Date already exist for this Employee');
                        }
                    } else {
                        $fail('Date requested doesnt exist for this Employee. File an attendance first to continue.');
                    }
          
                  
                  },
              ],
              'overtime_in' => ['required', 'string'],
              'overtime_out' => [
                  'required',
                  'string',
                  function ($attribute, $value, $fail) use ($request) {
                      $attendanceTime = strtotime($request->attendance_time);
                      $attendanceOut = strtotime($value);
          
                      if ($attendanceOut <= $attendanceTime) {
                          $fail('The overtime out time must be greater than the overtime in.');
                      }
                  },
              ],
            ];

        // Custom error messages
        $messages = [
            'overtime_date.required' => 'Overtime Date is required',
            'overtime_in.required' => 'Overtime in is required',
            'overtime_out.required' => 'Overtime Out is required',
        ];
        // Validate request
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->route('workDetails',  ['id' => $request->deployment_id,'parent_index' => 3])->withErrors($validator->errors())->withInput();
        }

        //check current user
        $user = \Auth::user()->id;

        $employee = Deployment::find($request->deployment_id);
        $schedule = $employee->schedule;
        $timeIn = Carbon::parse($request->overtime_in)->format('H:i:s');
        $timeOut = Carbon::parse($request->overtime_out)->format('H:i:s');
        $overTimeDuration = $this->computeOverTimeDuration($schedule, $timeIn, $timeOut);
      
        if($overTimeDuration >= 60){
            $attendance = Attendance::where('attendance_date',Carbon::parse($request->overtime_date)->format('Y-m-d'))
                                ->where('deployment_id', $request->deployment_id)
                                ->first();

            $overtime = new OverTime();
            $overtime->deployment_id = $request->deployment_id;
            $overtime->duration = Carbon::createFromTime(0, 0, 0)->addMinutes($overTimeDuration);
            $overtime->overtime_date =  Carbon::parse($request->overtime_date)->format('Y-m-d');
            $overtime->overtime_in = $timeIn;
            $overtime->overtime_out = $timeOut;
            $overtime->attendance_id = $attendance->id;
            $overtime->creator_id = $user;
            $overtime->updater_id = $user;
            $overtime->save();
        } else {
             //if error occurs rollback the data from it's previos state
             \DB::rollback();
             return back()->with("errorMsg", "Note: We do not acknowledge overtime periods shorter than 1 hour.");
        }
           
        $log = new Log();
        $log->log = "User " . \Auth::user()->email . " create overtime " . $overtime->id . " at " . Carbon::now();
        $log->creator_id =  \Auth::user()->id;
        $log->updater_id =  \Auth::user()->id;
        $log->save();
        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();

            return redirect()->route('workDetails',  ['id' => $request->deployment_id,'parent_index' => 3])->with('successMsg', 'Overtime Data Save Successful');
        } catch (\Exception $e) {
            //if error occurs rollback the data from it's previos state
            \DB::rollback();
            return redirect()->route('workDetails',  ['id' => $request->deployment_id,'parent_index' => 3])->withErrors($e->getMessage());
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
        $deployment = Deployment::withTrashed()->findOrFail($id);
        $overtimes = OverTime::withTrashed()->where('deployment_id', $deployment->id)->get();
        return view('overtime.edit', [
            'deployment' => $deployment,
            'overtimes' => $overtimes
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
        //
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

         //delete OverTime
         $overtime = Overtime::findOrFail($id);
         $overtime->delete();
 
         $log = new Log();
         $log->log = "User " . \Auth::user()->email . " delete overtime " . $overtime->id . " at " . Carbon::now();
         $log->creator_id =  \Auth::user()->id;
         $log->updater_id =  \Auth::user()->id;
         $log->save();
    }


    public function bulkOverTime(Request $request)
    {
        //prevent other user to access to this page
        $this->authorize("isHROrAdmin");

        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();

        try {
         
        $validator = Validator::make($request->all(), [
            'excel_file' => 'required|mimes:xlsx,xls',
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }


        $overTimeImport = new OverTimeImport;
        $excelFile = $request->file('excel_file');
        $sheet = Excel::import($overTimeImport, $excelFile);
        $rows = $overTimeImport->data;

        if (count($rows) > 200) {
            return redirect()->back()->withErrors(['Too many rows in the Excel file. Maximum allowed is 200.'])->withInput();
        }
      
        if (count($rows) <= 0) {
            return redirect()->back()->withErrors(['No data found on the Excel file.'])->withInput();
        }
       
        $errors = [];
        $validRows = [];
    
        foreach ($rows as $key => $row) {
            $validator = Validator::make($row, [
                'employee_no' => [
                    'required',
                    function ($attribute, $value, $fail) {
                        $deployment = Deployment::where('status','new')
                        ->where('reference_no', $value)
                        ->first();
                      
                        if ($deployment != null) {  
                            $schedule = Schedule::where('deployment_id',$deployment->id)->first();
                            if(!$schedule){
                                $fail('Employee doesnt any schedule record.');
                            }
                        } else {
                            $fail('Employee doesnt exist.');
                        }
                    },
                ],
                'overtime_in' => 'required|date_format:H:i:s',
                'overtime_out' => [
                    'required',
                    'date_format:H:i:s',
                    'after:overtime_in',
                    function ($attribute, $value, $fail) use ($row) {
                        $attendanceTime = strtotime($row['overtime_in']);
                        $attendanceOut = strtotime($value);

                        $deployment = Deployment::where('status','new')
                        ->where('reference_no', $row['employee_no'])
                        ->first();

                        if ($deployment != null) {  
                            $schedule = $employee->schedule;
                            $timeIn = Carbon::parse($row['overtime_in'])->format('H:i:s');
                            $timeOut = Carbon::parse($value)->format('H:i:s');
                            $overTimeDuration = $this->computeOverTimeDuration($schedule, $timeIn, $timeOut);
    
                            
                            if ($attendanceOut <= $attendanceTime) {
                                $fail('The overtime out time must be greater than the overtime in.');
                            }

                            if($overTimeDuration < 60) {
                                $fail( "Note: We do not acknowledge overtime periods shorter than 1 hour.");
                            }
                        }
                    },
                ],
                'overtime_date' => [
                    'required',
                    'date_format:Y-m-d',
                    'not_weekend',
                    'before_or_equal:' . now()->format('Y-m-d'),
                    function ($attribute, $value, $fail) use ($row) {
                        $deployment = Deployment::where('status','new')
                        ->where('reference_no', $row['employee_no'])
                        ->first();
                        if($deployment != null) {
                            $attendance = Attendance::where('attendance_date',Carbon::parse($value)->format('Y-m-d'))
                            ->where('deployment_id', $deployment->id)
                            ->where('status', 'Present')
                            ->exists();
      
                            $overtime = OverTime::where('overtime_date',Carbon::parse($value)->format('Y-m-d'))
                            ->where('deployment_id', $deployment->id)
                            ->exists();
        
                            if($attendance) {
                                if ($overtime) {
                                    $fail('Overtime Date already exist for this Employee');
                                }
                            } else {
                                $fail('Date requested doesnt exist for this Employee. File an attendance first to continue.');
                            }
                        }
                       
                    },
                ],
            ]);
            if ($validator->fails()) {
               
                $errors[] = [
                    'row' => $key + 1,
                    'errors' => $validator->messages()->all(),
                ];
            } else {
                $validRows[] = $row;
            }
        }

        if (!empty($errors)) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = 'Row ' . $error['row'] . ': ' . implode(', ', $error['errors']);
            }
            return redirect()->back()->withErrors(['errors' => $errorMessages])->withInput();
        }
    
        if (count($validRows) > 200) {
            return redirect()->back()->withErrors(['Too many rows in the Excel file. Maximum allowed is 200.'])->withInput();
        }
    
     
    
        // Process the valid rows
        foreach ($validRows as $row) {
            // Create a new attendance record
            $employee = Deployment::where('reference_no',  $row['employee_no'])->first();
            //check current user
            $user = \Auth::user()->id;
            $schedule = $employee->schedule;
            $timeIn = Carbon::parse($request->overtime_in)->format('H:i:s');
            $timeOut = Carbon::parse($request->overtime_out)->format('H:i:s');
            $overTimeDuration = $this->computeOverTimeDuration($schedule, $timeIn, $timeOut);
        
            $attendance = Attendance::where('attendance_date',Carbon::parse($row['overtime_date'])->format('Y-m-d'))
                                ->where('deployment_id', $employee->id)
                                ->first();

            $overtime = new OverTime();
            $overtime->deployment_id = $employee->id;
            $overtime->duration = Carbon::createFromTime(0, 0, 0)->addMinutes($overTimeDuration);
            $overtime->overtime_date =  Carbon::parse($request->overtime_date)->format('Y-m-d');
            $overtime->overtime_in = $timeIn;
            $overtime->overtime_out = $timeOut;
            $overtime->attendance_id = $attendance->id;
            $overtime->creator_id = $user;
            $overtime->updater_id = $user;
            $overtime->save();
         
            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();
        }

        
        return redirect()->route('overtime.index')->with('successMsg', 'OverTime Data Save Successful');
        } catch (\Exception $e) {
            //if error occurs rollback the data from it's previos state
            \DB::rollback();
            return back()->withErrors($e->getMessage());
        }
    }


    public function computeOverTimeDuration($schedule, $timeIn, $timeOut)
    {
        // Validate input times
        if ($timeIn < $schedule->time_out || $timeIn > $schedule->time_out) {
            throw new \InvalidArgumentException('Over Time-in should be equal in schedule time out ');
        }

        if ($timeOut <= $timeIn) {
            throw new \InvalidArgumentException('Over Time-out must be greater than Over Time-in');
        }

        // Convert times to Carbon instances for easy manipulation
        $scheduleTimeOut = Carbon::parse($schedule->time_out);
        $timeInCarbon = Carbon::parse($timeIn);
        $timeOutCarbon = Carbon::parse($timeOut);

        // Compute late time duration
        $overTimeDuration = 0;
        if ($timeInCarbon == $scheduleTimeOut) {
            $overTimeDuration = $timeInCarbon->diffInMinutes($timeOutCarbon);
        }

        // Return late time duration in minutes
        return $overTimeDuration;
    }
}
