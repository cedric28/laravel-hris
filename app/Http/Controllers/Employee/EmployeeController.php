<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Log;
use App\Employee;
use App\EmploymentType;
use App\Industry;
use Carbon\Carbon;
use Validator;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employees = Employee::all();
        $InactiveEmployee = Employee::onlyTrashed()->get();
        return view("employee.index", [
            'employees' => $employees,
            'InactiveEmployee' => $InactiveEmployee
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
        $this->authorize("isAdmin");

        return view("employee.create");
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
        $this->authorize("isAdmin");
        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();

        try {
            //validate request value
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:50|unique:employees,name',
                'nickname' => 'required|string|max:10',
                'address' => 'required|string|max:50',
                'contact_number' => 'required|digits:10',
                'email' => 'required|email|max:50'
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }

            //check current user
            $user = \Auth::user()->id;

            //save employee
            $employee = new Employee();
            $employee->reference_no = $this->generateUniqueCode();
            $employee->name = $request->name;
            $employee->nickname = $request->nickname;
            $employee->address = $request->address;
            $employee->contact_number = $request->contact_number;
            $employee->email = $request->email;
            $employee->creator_id = $user;
            $employee->updater_id = $user;
            $employee->save();

            $log = new Log();
            $log->log = "User " . \Auth::user()->email . " create employee " . $employee->reference_no . " at " . Carbon::now();
            $log->creator_id =  \Auth::user()->id;
            $log->updater_id =  \Auth::user()->id;
            $log->save();
            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();

            return redirect()->route('employee.create')
                ->with('successMsg', 'Employeee Save Successful');
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
        $this->authorize("isAdmin");

        $employee = Employee::withTrashed()->findOrFail($id);

        return view('employee.show', [
            'employee' => $employee
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
        $this->authorize("isAdmin");

        $employee = Employee::withTrashed()->findOrFail($id);
        $employmentTypes = EmploymentType::all();
        $industries = Industry::all();

        return view('employee.edit', [
            'employee' => $employee,
            'employmentTypes' => $employmentTypes,
            'industries' => $industries
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
        $this->authorize("isAdmin");

        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();

        try {
            //check employee if exist
            $employee = Employee::withTrashed()->findOrFail($id);

            //validate the request value
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|unique:employees,name,' . $employee->id,
                'nickname' => 'required|string|max:10',
                'address' => 'required|string|max:50',
                'contact_number' => 'required|digits:10',
                'email' => 'required|email|max:50'
            ]);
            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }

            //check current user
            $user = \Auth::user()->id;

            //save the update value
            $employee->name = $request->name;
            $employee->short_name = $request->short_name;
            $employee->address = $request->address;
            $employee->contact_number = $request->contact_number;
            $employee->email = $request->email;
            $employee->updater_id = $user;
            $employee->update();

            $log = new Log();
            $log->log = "User " . \Auth::user()->email . " edit employee " . $employee->reference_no . " at " . Carbon::now();
            $log->creator_id =  \Auth::user()->id;
            $log->updater_id =  \Auth::user()->id;
            $log->save();
            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();

            return back()->with("successMsg", "Employee Update Successfully");
        } catch (\Exception $e) {
            //if error occurs rollback the data from it's previos state
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
        $this->authorize("isAdmin");

        //delete employee
        $employee = Employee::findOrFail($id);
        $employee->delete();

        $log = new Log();
        $log->log = "User " . \Auth::user()->email . " delete employee " . $employee->reference_no . " at " . Carbon::now();
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

            $employee = Employee::onlyTrashed()->findOrFail($id);

            /* Restore employee */
            $employee->restore();

            $log = new Log();
            $log->log = "User " . \Auth::user()->email . " restore employee " . $employee->reference_no . " at " . Carbon::now();
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

    public function generateUniqueCode()
    {
        do {
            $reference_no = random_int(1000000000, 9999999999);
        } while (Employee::where("reference_no", "=", $reference_no)->first());

        return $reference_no;
    }
}