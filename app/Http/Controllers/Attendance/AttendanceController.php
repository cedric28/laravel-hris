<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Attendance;
use App\Deployment;
use App\Log;
use Validator, Hash, DB;
use Carbon\Carbon;

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
              $messages = [
                  'attendance_date.required' => 'Attendance Date is required',
                  'attendance_time.required' => 'Attendance Time is required'
              ];
              //validate request value
              $validator = Validator::make($request->all(), [
                  'attendance_date' => 'required|string',
                  'attendance_date' => 'required|string'
              ], $messages);
  
              if ($validator->fails()) {
                  return back()->withErrors($validator->errors())->withInput();
              }
  
              //check current user
              $user = \Auth::user()->id;
  
              //save attendance
              $attendance = new Attendance();
              $attendance->attendance_time = Carbon::parse($request->attendance_time)->format('H:i:s');
              $attendance->attendance_date = Carbon::parse($request->attendance_date)->format('Y-m-d');
              $attendance->deployment_id = $request->deployment_id;
              $attendance->status = 1;
              $attendance->creator_id = $user;
              $attendance->updater_id = $user;
              $attendance->save();
  
              $log = new Log();
              $log->log = "User " . \Auth::user()->email . " create attendance " . $attendance->id . " at " . Carbon::now();
              $log->creator_id =  \Auth::user()->id;
              $log->updater_id =  \Auth::user()->id;
              $log->save();
              /*
              | @End Transaction
              |---------------------------------------------*/
              \DB::commit();
  
              return redirect()->route('attendance.edit', $deployment->id);
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
        return view('attendance.edit', [
            'deployment' => $deployment
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
}
