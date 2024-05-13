<?php

namespace App\Http\Controllers\Schedule;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Schedule;
use App\Deployment;
use Validator, Hash, DB;
use Carbon\Carbon;
use App\Log;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $schedules = Schedule::all();
        $InactiveSchedule = Schedule::onlyTrashed()->get();

        return view("schedule.index", [
            'schedules' => $schedules,
            'InactiveSchedule' => $InactiveSchedule
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

         $deployments = Deployment::all();
 
         return view("schedule.create",[
             'deployments' => $deployments
         ]);
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
 
             $messages = [
                 'deployment_id.required' => 'Please select an Employee',
                 'slug.required' => 'Input a schedule',
             ];
             //validate request value
             $validator = Validator::make($request->all(), [
                 'deployment_id' => 'required|integer',
                 'slug' => 'required|string',
                 'time_in' => 'required|string',
                 'time_out' => [
                    'required',
                    'string',
                    function ($attribute, $value, $fail) use ($request) {
                        $scheduleTime = strtotime($request->time_in);
                        $scheduleOut = strtotime($value);
            
                        if ($scheduleOut <= $scheduleTime) {
                            $fail('The schedule out time must be greater than the schedule time in.');
                        }
                    },
                 ]
             ], $messages);
 
             if ($validator->fails()) {
                 return back()->withErrors($validator->errors())->withInput();
             }
 
             //check current user
             $user = \Auth::user()->id;
 
             //save schedule
             $schedule = new Schedule();
             $schedule->deployment_id = $request->deployment_id;
             $schedule->slug = $request->slug;
             $schedule->time_in = Carbon::parse($request->time_in)->format('H:i:s');
             $schedule->time_out = Carbon::parse($request->time_out)->format('H:i:s');
             $schedule->creator_id = $user;
             $schedule->updater_id = $user;
             $schedule->save();
 
             $log = new Log();
             $log->log = "User " . \Auth::user()->email . " create deployment " . $schedule->id . " at " . Carbon::now();
             $log->creator_id =  \Auth::user()->id;
             $log->updater_id =  \Auth::user()->id;
             $log->save();
             /*
             | @End Transaction
             |---------------------------------------------*/
             \DB::commit();
 
             return redirect()->route('schedule.edit', $schedule->id);
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
        //prevent other user to access to this page
        $this->authorize("isHROrAdmin");

        $schedule = Schedule::withTrashed()->findOrFail($id);
   
        return view('schedule.show', [
            'schedule' => $schedule
        ]);
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

        $schedule = Schedule::withTrashed()->findOrFail($id);
        $deployments = Deployment::all();
   
        return view('schedule.edit', [
            'schedule' => $schedule,
            'deployments' => $deployments
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
        //prevent other user to access to this page
        $this->authorize("isHROrAdmin");
        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();

        try {
            $schedule = Schedule::withTrashed()->findOrFail($id);

            $messages = [
                'deployment_id.required' => 'Please select an Employee',
                'slug.required' => 'Input a schedule',
                'deployment_id.unique' => 'This employee already has an assigned schedule',
            ];
            //validate request value
            $validator = Validator::make($request->all(), [
                'deployment_id' => 'required|integer|unique:schedules,deployment_id,' . $schedule->id,
                'slug' => 'required|string',
                'time_in' => 'required|string',
                'time_out' => [
                    'required',
                    'string',
                    function ($attribute, $value, $fail) use ($request) {
                        $scheduleTime = strtotime($request->time_in);
                        $scheduleOut = strtotime($value);
            
                        if ($scheduleOut <= $scheduleTime) {
                            $fail('The schedule out time must be greater than the schedule time in.');
                        }
                    },
                ]
            ], $messages);

            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }

            $schedule->deployment_id = $request->deployment_id;
            $schedule->slug = $request->slug;
            $schedule->time_in = Carbon::parse($request->time_in)->format('H:i:s');
            $schedule->time_out = Carbon::parse($request->time_out)->format('H:i:s');
            $schedule->updater_id = \Auth::user()->id;
            $schedule->save();


            $log = new Log();
            $log->log = "User " . \Auth::user()->email . " edit schedule " .  $schedule->id . " at " . Carbon::now();
            $log->creator_id =  \Auth::user()->id;
            $log->updater_id =  \Auth::user()->id;
            $log->save();

            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();

            return redirect()->route('schedule.edit', $schedule->id)
                ->with('successMsg', 'Schedule Data update Successfully');
        } catch (\Exception $e) {
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
        //delete Schedule
        $schedule = Schedule::findOrFail($id);
        $schedule->delete();

        $log = new Log();
        $log->log = "User " . \Auth::user()->email . " delete schedule " .  $schedule->id . " at " . Carbon::now();
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

            $schedule = Schedule::onlyTrashed()->findOrFail($id);

            /* Restore schedule */
            $schedule->restore();

            $log = new Log();
            $log->log = "User " . \Auth::user()->email . " restore schedule " .  $schedule->id . " at " . Carbon::now();
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
