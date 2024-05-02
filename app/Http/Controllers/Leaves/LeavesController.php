<?php

namespace App\Http\Controllers\Leaves;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Leave;
use App\Deployment;
use App\LeaveType;
use App\LeaveStatus;
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
        $leaves = Leave::all();
        $InactiveLeave = Leave::onlyTrashed()->get();

        return view("leaves.index", [
            'leaves' => $leaves,
            'InactiveLeave' => $InactiveLeave
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

            $messages = [
                'employee_id.required' => 'Please select a Employee',
                'client_id.required' => 'Please select a Client',
                'employment_type_id.required' => 'Please select a Employment Type'
            ];
            //validate request value
            $validator = Validator::make($request->all(), [
                'employee_id' => 'required|integer',
                'client_id' => 'required|integer',
                'employment_type_id' => 'required|integer',
                'position' => 'required|string|max:50',
                'start_date' => 'required|string'
            ], $messages);

            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }

            //check current user
            $user = \Auth::user()->id;

            //save deployment
            $deployment = new Deployment();
            // $employee->reference_no = $this->generateUniqueCode();
            $deployment->employee_id = $request->employee_id;
            $deployment->employment_type_id = $request->employment_type_id;
            $deployment->client_id = $request->client_id;
            $deployment->position = $request->position;
            $deployment->start_date = Carbon::parse($request->start_date)->format('Y-m-d');
            $deployment->end_date = Carbon::parse($request->end_date)->format('Y-m-d');
            $deployment->creator_id = $user;
            $deployment->updater_id = $user;
            $deployment->save();

            $log = new Log();
            $log->log = "User " . \Auth::user()->email . " create deployment " . $deployment->id . " at " . Carbon::now();
            $log->creator_id =  \Auth::user()->id;
            $log->updater_id =  \Auth::user()->id;
            $log->save();
            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();

            return redirect()->route('deployment.edit', $deployment->id);
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
        $employmentTypes = EmploymentType::all();
        $clients = Client::all();
        $employees = Employee::all();
   
        return view('deployment.edit', [
            'clients' => $clients,
            'employees' => $employees,
            'employmentTypes' => $employmentTypes,
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
         /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();

        try {
            $deployment = Deployment::withTrashed()->findOrFail($id);

            $messages = [
                'employee_id.required' => 'Please select a Employee',
                'client_id.required' => 'Please select a Client',
                'employment_type_id.required' => 'Please select a Employment Type'
            ];
            //validate request value
            $validator = Validator::make($request->all(), [
                'employee_id' => 'required|integer',
                'client_id' => 'required|integer',
                'employment_type_id' => 'required|integer',
                'position' => 'required|string|max:50',
                'start_date' => 'required|string|max:50'
            ], $messages);

            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }

          
            $deployment->employee_id = $request->employee_id;
            $deployment->employment_type_id = $request->employment_type_id;
            $deployment->client_id = $request->client_id;
            $deployment->position = $request->position;
            $deployment->start_date = Carbon::parse($request->start_date)->format('Y-m-d');
            $deployment->end_date = Carbon::parse($request->end_date)->format('Y-m-d');
            $deployment->updater_id = \Auth::user()->id;
            $deployment->save();

            $log = new Log();
            $log->log = "User " . \Auth::user()->email . " edit deployment " .  $deployment->id . " at " . Carbon::now();
            $log->creator_id =  \Auth::user()->id;
            $log->updater_id =  \Auth::user()->id;
            $log->save();

            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();

            return redirect()->route('deployment.edit', $deployment->id)
                ->with('successMsg', 'Deployment Data update Successfully');
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

         //delete deployment
         $deployment = Deployment::findOrFail($id);
         $deployment->delete();
 
         $log = new Log();
         $log->log = "User " . \Auth::user()->email . " delete deployment " . $deployment->id . " at " . Carbon::now();
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

            $deployment = Deployment::onlyTrashed()->findOrFail($id);

            /* Restore deployment */
            $deployment->restore();

            $log = new Log();
            $log->log = "User " . \Auth::user()->email . " restore deployment " . $deployment->id . " at " . Carbon::now();
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
