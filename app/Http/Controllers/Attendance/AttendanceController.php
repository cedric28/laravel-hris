<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Attendance;
use App\Deployment;
use App\Log;
use App\LateTime;
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
        //
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
                'attendance_date' => [
                    'required',
                    'string',
                    function ($attribute, $value, $fail) use ($request) {
                        $attendance = Attendance::where('attendance_date',Carbon::parse($value)->format('Y-m-d'))
                        ->where('deployment_id', $request->deployment_id)
                        ->exists();
            
                        if ($attendance) {
                            $fail('Attendance Date already exist for this Employee');
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
                return back()->withErrors($validator->errors())->withInput();
            }

            //check current user
            $user = \Auth::user()->id;
            $attendanceDate = Carbon::parse($request->attendance_date); 
    
            //save attendance
            $attendance = new Attendance();
            $attendance->attendance_time = Carbon::parse($request->attendance_time)->format('H:i:s');
            $attendance->attendance_out = Carbon::parse($request->attendance_out)->format('H:i:s');
            $attendance->attendance_date = Carbon::parse($request->attendance_date)->format('Y-m-d');
            $attendance->day_of_week =  $attendanceDate->dayOfWeek == 7 ? 0 : $attendanceDate->dayOfWeek + 1;
            $attendance->deployment_id = $request->deployment_id;
            $attendance->status = 1;
            $attendance->creator_id = $user;
            $attendance->updater_id = $user;
            $attendance->save();

            $employee = Deployment::find($request->deployment_id);
            $schedule = $employee->schedule;
            $timeIn = Carbon::parse($request->attendance_time)->format('H:i:s');
            $timeOut = Carbon::parse($request->attendance_out)->format('H:i:s');
            $lateTimeDuration = $this->computeLateTimeDuration($schedule, $timeIn, $timeOut);

            if($lateTimeDuration > 0){
                $late = new LateTime();
                $late->deployment_id = $request->deployment_id;
                $late->attendance_id = $attendance->id;
                $late->duration = Carbon::createFromTime(0, $lateTimeDuration, 0)->format('H:i:s');
                $late->latetime_date =  Carbon::parse($request->attendance_date)->format('Y-m-d');
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
  
              return redirect()->route('attendance.edit', $deployment->id)->with('successMsg', 'Attendance Data Save Successful');
          } catch (\Exception $e) {
              //if error occurs rollback the data from it's previos state
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
