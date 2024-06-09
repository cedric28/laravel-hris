<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Imports\AttendanceImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Attendance;
use App\Deployment;
use App\Log;
use App\LateTime;
use App\Schedule;
use Validator, Hash, DB;
use Carbon\Carbon;
use App\Rules\TimeNotGreaterThan;
use Illuminate\Validation\Rule;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("attendance.index");
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

            $schedule = Schedule::where('deployment_id',$request->deployment_id)->first();

            if(!$schedule){
                return back()->with("errorMsg", "Note: Please set a schedule first."); 
            }

              $deployment = Deployment::withTrashed()->findOrFail($request->deployment_id);
            
              $rules = [
                'attendance_date' => [
                    'required',
                    'string',
                    function ($attribute, $value, $fail) use ($request, $deployment) {
                        $attendanceDate = Carbon::parse($value);
                        $attendanceDateFormatted = $attendanceDate->format('Y-m-d');

                        // Check if the attendance date falls on a weekend
                        if ($attendanceDate->isWeekend()) {
                            $fail('Attendance Date cannot be on a weekend');
                        }
                        
                        $attendance = Attendance::where('attendance_date', $attendanceDateFormatted)
                            ->where('deployment_id', $request->deployment_id)
                            ->where('status','Present')
                            ->exists();
                    
                        if ($attendance) {
                            $fail('Attendance Date already exists for this Employee');
                        }
                    
                        if (!$attendanceDate->between($deployment->start_date, $deployment->end_date)) {
                            $fail('Attendance Date should be within the contract period: Start Date '.$deployment->start_date.' and End Date '.$deployment->end_date);
                        }
                    },
                ],
                'attendance_time' => ['required', 'string'],
                'attendance_out' => [
                    'required',
                    'string',
                    function ($attribute, $value, $fail) use ($request) {
                        $attendanceTime = strtotime($request->attendance_time);
                        $attendanceOut = strtotime($value);
            
                        if ($attendanceOut <= $attendanceTime) {
                            $fail('The attendance out time must be greater than the attendance time in.');
                        }

                        if ($attendanceTime >= $attendanceOut) {
                            $fail('The attendance time in must be less than the attendance time out.');
                        }
                    },
                ],
            ];

            // Custom error messages
            $messages = [
                'attendance_date.required' => 'Attendance Date is required',
                'attendance_time.required' => 'Attendance Time in is required',
                'attendance_out.required' => 'Attendance Time Out is required',
            ];
             // Validate request
            $validator = Validator::make($request->all(), $rules, $messages);
  
            if ($validator->fails()) {
                return redirect()->route('workDetails', ['id' => $request->deployment_id,'parent_index' => 2])->withErrors($validator->errors())->withInput();
            }

            //check current user
            $user = \Auth::user()->id;
            $attendanceDate = Carbon::parse($request->attendance_date); 
            $attendanceTime = strtotime($request->attendance_time);
            $attendanceOut = strtotime($request->attendance_out);

            $totalHours = ($attendanceOut - $attendanceTime) / 3600; 
            //save attendance
            $isExist = Attendance::where('attendance_date', Carbon::parse($request->attendance_date)->format('Y-m-d'))
                            ->where('deployment_id', $request->deployment_id)
                            ->first();

            $attendanceId = "";
            if($isExist){
                $isExist->attendance_time = Carbon::parse($request->attendance_time)->format('H:i:s');
                $isExist->attendance_out = Carbon::parse($request->attendance_out)->format('H:i:s');
                $isExist->attendance_date = Carbon::parse($request->attendance_date)->format('Y-m-d');
                $isExist->day_of_week =  $attendanceDate->dayOfWeek == 7 ? 0 : $attendanceDate->dayOfWeek + 1;
                $isExist->hours_worked = $totalHours = ($totalHours <= 4) ? $totalHours : (($totalHours >= 5 && $totalHours <= 9) ? ($totalHours - 1) : 8);
                $isExist->deployment_id = $request->deployment_id;
                $isExist->status = 'Present';
                $isExist->updater_id = $user;
                $isExist->save();

                $attendanceId =  $isExist->id;
            } else {
                $attendance = new Attendance();
                $attendance->attendance_time = Carbon::parse($request->attendance_time)->format('H:i:s');
                $attendance->attendance_out = Carbon::parse($request->attendance_out)->format('H:i:s');
                $attendance->attendance_date = Carbon::parse($request->attendance_date)->format('Y-m-d');
                $attendance->day_of_week =  $attendanceDate->dayOfWeek == 7 ? 0 : $attendanceDate->dayOfWeek + 1;
                $attendance->hours_worked = $totalHours = ($totalHours <= 4) ? $totalHours : (($totalHours >= 5 && $totalHours <= 9) ? ($totalHours - 1) : 8);
                $attendance->deployment_id = $request->deployment_id;
                $attendance->status = 'Present';
                $attendance->creator_id = $user;
                $attendance->updater_id = $user;
                $attendance->save();

                $attendanceId = $attendance->id;
            }
           

            $employee = Deployment::find($request->deployment_id);
            $schedule = $employee->schedule;
            $timeIn = Carbon::parse($request->attendance_time)->format('H:i:s');
            $timeOut = Carbon::parse($request->attendance_out)->format('H:i:s');
            $lateTimeDuration = $this->computeLateTimeDuration($schedule, $timeIn, $timeOut);

            if($lateTimeDuration > 0){
                $late = new LateTime();
                $late->deployment_id = $request->deployment_id;
                $late->attendance_id = $attendanceId;
                $late->duration = Carbon::createFromTime(0, $lateTimeDuration, 0)->format('H:i:s');
                $late->latetime_date =  Carbon::parse($request->attendance_date)->format('Y-m-d');
                $late->creator_id = $user;
                $late->updater_id = $user;
                $late->save();
            }
             
            $log = new Log();
            $log->log = "User " . \Auth::user()->email . " create attendance " . $attendanceId . " at " . Carbon::now();
            $log->creator_id =  \Auth::user()->id;
            $log->updater_id =  \Auth::user()->id;
            $log->save();
              /*
              | @End Transaction
              |---------------------------------------------*/
              \DB::commit();
  
              return redirect()->route('workDetails', ['id' => $request->deployment_id,'parent_index' => 2])->with('successMsg', 'Attendance Data Save Successful');
          } catch (\Exception $e) {
              //if error occurs rollback the data from it's previos state
              \DB::rollback();
              return redirect()->route('workDetails', ['id' => $request->deployment_id,'parent_index' => 2])->withErrors($e->getMessage());
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
        $lates = LateTime::withTrashed()->where('deployment_id', $deployment->id)->get();
        return view('attendance.edit', [
            'deployment' => $deployment,
            'lates' => $lates
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

         //delete Attendance
         $attendance = Attendance::findOrFail($id);
        // Create a new instance of Attendance model
        $newAttendance = new Attendance();
        $attributes = $attendance->getAttributes();
        unset($attributes['id']);
        $newAttendance->fill($attributes);
        $newAttendance->attendance_time = '00:00:00';
        $newAttendance->attendance_out = '00:00:00';
        $newAttendance->hours_worked = 0;
        $newAttendance->status = 'Absent';

        // Save the new instance
        $newAttendance->save();

         $late = LateTime::where('attendance_id', $attendance->id)->first();

         if ($late) {
            $late->delete();
         }

         $attendance->delete();
 
         $log = new Log();
         $log->log = "User " . \Auth::user()->email . " delete attendance " . $attendance->id . " at " . Carbon::now();
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

            $attendance = Attendance::onlyTrashed()->findOrFail($id);

            /* Restore attendance */
            $attendance->restore();

            $log = new Log();
            $log->log = "User " . \Auth::user()->email . " restore attendance " . $attendance->id . " at " . Carbon::now();
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

    public function bulkAttendance(Request $request)
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


        $attendanceImport = new AttendanceImport;
        $excelFile = $request->file('excel_file');
        $sheet = Excel::import($attendanceImport, $excelFile);
        $rows = $attendanceImport->data;

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

                        if ($attendanceTime >= $attendanceOut) {
                            $fail('The attendance time in must be less than the attendance time out.');
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
                            $attendanceDate = Carbon::parse($value);
                            $attendanceDateFormatted = $attendanceDate->format('Y-m-d');
                            
                            $attendance = Attendance::where('attendance_date', $attendanceDateFormatted)
                                ->where('status','Present')
                                ->where('deployment_id', $request->deployment_id)
                                ->exists();
                
                            if ($attendance) {
                                $fail('Attendance Date already exist for this Employee');
                            }
    
                            if (!$attendanceDate->between($deployment->start_date, $deployment->end_date)) {
                                $fail('Attendance Date should be within the contract period: Start Date '.$deployment->start_date.' and End Date '.$deployment->end_date);
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

            $isExist = Attendance::where('attendance_date', Carbon::parse($row['attendance_date'])->format('Y-m-d'))
            ->where('deployment_id', $request->deployment_id)
            ->first();
            $attendanceId = "";
            if($isExist){
                $isExist->attendance_time = Carbon::parse($row['attendance_time'])->format('H:i:s');
                $isExist->attendance_out = Carbon::parse($row['attendance_out'])->format('H:i:s');
                $isExist->attendance_date = $attendanceDate->format('Y-m-d');
                $isExist->day_of_week =  $attendanceDate->dayOfWeek == 7 ? 0 : $attendanceDate->dayOfWeek + 1;
                $isExist->deployment_id = $employee->id;
                $isExist->hours_worked =$totalHours = ($totalHours <= 4) ? $totalHours : (($totalHours >= 5 && $totalHours <= 9) ? ($totalHours - 1) : 8);
                $isExist->status = 'Present';
                $isExist->updater_id = $user;
                $isExist->save();

                $attendanceId = $isExist->id;
            } else {
                //save attendance
              $attendance = new Attendance();
              $attendance->attendance_time = Carbon::parse($row['attendance_time'])->format('H:i:s');
              $attendance->attendance_out = Carbon::parse($row['attendance_out'])->format('H:i:s');
              $attendance->attendance_date = $attendanceDate->format('Y-m-d');
              $attendance->day_of_week =  $attendanceDate->dayOfWeek == 7 ? 0 : $attendanceDate->dayOfWeek + 1;
              $attendance->deployment_id = $employee->id;
              $attendance->hours_worked =$totalHours = ($totalHours <= 4) ? $totalHours : (($totalHours >= 5 && $totalHours <= 9) ? ($totalHours - 1) : 8);
              $attendance->status = 'Present';
              $attendance->creator_id = $user;
              $attendance->updater_id = $user;
              $attendance->save();

              $attendanceId = $attendance->id;
            }
            
            $employee = Deployment::find($employee->id);
            $schedule = $employee->schedule;
            
            $timeIn = Carbon::parse($row['attendance_time'])->format('H:i:s');
            $timeOut = Carbon::parse($row['attendance_out'])->format('H:i:s');
            $lateTimeDuration = $this->computeLateTimeDuration($schedule, $timeIn, $timeOut);
  
            if($lateTimeDuration > 0){
                $isExist = LateTime::where('latetime_date', Carbon::parse($row['attendance_date'])->format('Y-m-d'))
                ->where('deployment_id',  $employee->id)
                ->first();

                if($isExist) {
                    $isExist->delete();
                }

                $late = new LateTime();
                $late->deployment_id = $employee->id;
                $late->attendance_id = $attendanceId;
                $late->duration = Carbon::createFromTime(0, $lateTimeDuration, 0)->format('H:i:s');
                $late->latetime_date =  Carbon::parse($row['attendance_date'])->format('Y-m-d');
                $late->creator_id = $user;
                $late->updater_id = $user;
                $late->save();
            } else {
                $isExist = LateTime::where('latetime_date', Carbon::parse($row['attendance_date'])->format('Y-m-d'))
                ->where('deployment_id',  $employee->id)
                ->first();

                if($isExist) {
                    $isExist->delete();
                }
            }
               
            $log = new Log();
            $log->log = "User " . \Auth::user()->email . " create attendance " . $attendanceId . " at " . Carbon::now();
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

    public function computeLateTimeDuration($schedule, $timeIn, $timeOut)
    {
        // Validate input times
        // if ($timeIn < $schedule->time_in) {
        //     throw new \InvalidArgumentException('Time in cannot be less than schedule time in');
        // }
        // if ($timeOut <= $schedule->time_out) {
        //     throw new \InvalidArgumentException('Time out must be greater than schedule time out');
        // }

        // Convert times to Carbon instances for easy manipulation
        $scheduleTimeIn = Carbon::parse($schedule->time_in);
        $scheduleTimeOut = Carbon::parse($schedule->time_out);
        $timeInCarbon = Carbon::parse($timeIn);
        $timeOutCarbon = Carbon::parse($timeOut);

        // Compute late time duration
        $lateTimeDuration = 0;
        if ($timeInCarbon > $scheduleTimeIn) {
            $lateTimeDuration = $timeInCarbon->diffInMinutes($scheduleTimeIn);
        }

        // Return late time duration in minutes
        return $lateTimeDuration;
    }
}
