<?php

namespace App\Http\Controllers\Leaves;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Leave;
use App\Deployment;
use App\LeaveType;
use App\LeaveStatus;
use App\Attendance;
use App\Log;
use Validator, Hash, DB;
use Carbon\Carbon;

class LeavesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $leaves = Leave::all();
        // $InactiveLeave = Leave::onlyTrashed()->get();

        // return view("leaves.index", [
        //     'leaves' => $leaves,
        //     'InactiveLeave' => $InactiveLeave
        // ]);
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

        $leaves = Leave::all();
        $leaveStatuses = LeaveStatus::all();
        $leaveTypes = LeaveType::all();

        return view("leaves.create",[
            'leaves' => $leaves,
            'leaveStatuses' => $leaveStatuses,
            'leaveTypes' => $leaveTypes
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
            $deployment = Deployment::withTrashed()->findOrFail($request->deployment_id);
            $messages = [
                'leave_type_id.required' => 'Please select a Leave Type',
                'leave_type_id.unique' => 'This leave type has already been taken',
                'leave_date.required' => 'Leave Date is required',
                'leave_time.required' => 'Leave Time is required'
            ];
            //validate request value
            $validator = Validator::make($request->all(), [
                'leave_type_id' => 'required|integer',
                'leave_date' => [
                    'required',
                    'string',
                    function ($attribute, $value, $fail) use ($request) {
                      $attendance = Attendance::where('attendance_date',Carbon::parse($value)->format('Y-m-d'))
                        ->where('deployment_id', $request->deployment_id)
                        ->exists();
  
                      if($attendance) {
                        $fail('You have an attendace time in and out on this date.');
                      }
                    
                    },
                ],
                'leave_time' => 'required|string'
            ], $messages);

            if ($validator->fails()) {
                return redirect()->route('workDetails',  ['id' => $request->deployment_id,'parent_index' => 4])->withErrors($validator->errors())->withInput();
            }

            //check current user
            $user = \Auth::user()->id;

            //save leave
            $leave = new Leave();
            $leave->leave_time = Carbon::parse($request->leave_time)->format('H:i:s');
            $leave->leave_date = Carbon::parse($request->leave_date)->format('Y-m-d');
            $leave->leave_status_id = 1;
            $leave->leave_type_id = $request->leave_type_id;
            $leave->deployment_id = $request->deployment_id;
            $leave->creator_id = $user;
            $leave->updater_id = $user;
            $leave->save();

            $log = new Log();
            $log->log = "User " . \Auth::user()->email . " create leave " . $deployment->id . " at " . Carbon::now();
            $log->creator_id =  \Auth::user()->id;
            $log->updater_id =  \Auth::user()->id;
            $log->save();
            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();

            return redirect()->route('workDetails',  ['id' => $request->deployment_id,'parent_index' => 4])->with('successMsg', 'Leaves Data Save Successful');
        } catch (\Exception $e) {
            //if error occurs rollback the data from it's previos state
            \DB::rollback();
            return redirect()->route('workDetails',  ['id' => $request->deployment_id,'parent_index' => 4])->withErrors($e->getMessage());
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
        $this->authorize("isAdmin");
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
        $leaveTypes = LeaveType::all();
        $deployment = Deployment::withTrashed()->findOrFail($id);
        return view('leaves.edit', [
            'leaveTypes' => $leaveTypes,
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
        //prevent other user to access to this page
        $this->authorize("isHROrAdmin");
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

         //delete leave
         $leave = Leave::findOrFail($id);
         $leave->delete();
 
         $log = new Log();
         $log->log = "User " . \Auth::user()->email . " delete leave " . $leave->id . " at " . Carbon::now();
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

            $leave = Leave::onlyTrashed()->findOrFail($id);

            /* Restore leave */
            $leave->restore();

            $log = new Log();
            $log->log = "User " . \Auth::user()->email . " restore leave " . $leave->id . " at " . Carbon::now();
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
