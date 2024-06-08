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
                'attendance_time' => 'required|date_format:H:i:s',
                'attendance_out' => [
                    'required',
                    'date_format:H:i:s',
                    'after:attendance_time',
                    function ($attribute, $value, $fail) use ($row) {
                        $attendanceTime = strtotime($row['attendance_time']);
                        $attendanceOut = strtotime($value);
            
                        if ($attendanceOut <= $attendanceTime) {
                            $fail('The attendance out time must be greater than the attendance time in.');
                        }

                        $totalHours = ($attendanceOut - $attendanceTime) / 3600; // convert seconds to hours

                        if ($totalHours > 9) {
                            $fail('The total attendance time must be less than or equal to 9 hours.');
                        }
                    },
                ],
                'attendance_date' => [
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
                            ->exists();
                            if ($attendance) {
                                $fail('Attendance Date already exist for this Employee');
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
            $attendanceDate = Carbon::parse($row['attendance_date']); 
            $attendanceTime = strtotime($row['attendance_time']);
            $attendanceOut = strtotime($row['attendance_out']);
            $totalHours = ($attendanceOut - $attendanceTime) / 3600; 

              //save attendance
              $attendance = new Attendance();
              $attendance->attendance_time = Carbon::parse($row['attendance_time'])->format('H:i:s');
              $attendance->attendance_out = Carbon::parse($row['attendance_out'])->format('H:i:s');
              $attendance->attendance_date = $attendanceDate->format('Y-m-d');
              $attendance->day_of_week =  $attendanceDate->dayOfWeek == 7 ? 0 : $attendanceDate->dayOfWeek + 1;
              $attendance->deployment_id = $employee->id;
              $attendance->hours_worked =  $totalHours <= 4 ? $totalHours : $totalHours - 1; 
              $attendance->status = 1;
              $attendance->creator_id = $user;
              $attendance->updater_id = $user;
              $attendance->save();
  
              $employee = Deployment::find($employee->id);
              $schedule = $employee->schedule;
              
              $timeIn = Carbon::parse($row['attendance_time'])->format('H:i:s');
              $timeOut = Carbon::parse($row['attendance_out'])->format('H:i:s');
              $lateTimeDuration = $this->computeLateTimeDuration($schedule, $timeIn, $timeOut);
  
              if($lateTimeDuration > 0){
                  $late = new LateTime();
                  $late->deployment_id = $employee->id;
                  $late->attendance_id = $attendance->id;
                  $late->duration = Carbon::createFromTime(0, $lateTimeDuration, 0)->format('H:i:s');
                  $late->latetime_date =  Carbon::parse($row['attendance_date'])->format('Y-m-d');
                  $late->creator_id = $user;
                  $late->updater_id = $user;
                  $late->save();
              }
               
              $log = new Log();
              $log->log = "User " . \Auth::user()->email . " create attendance " . $attendance->id . " at " . Carbon::now();
              $log->creator_id =  \Auth::user()->id;
              $log->updater_id =  \Auth::user()->id;
              $log->save();
            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();
    
        }

        
        return redirect()->route('attendance.index')->with('successMsg', 'Attendance Data Save Successful');
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
