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
use App\Gender;
use App\CivilStatus;
use App\EmploymentHistory;
use App\EducationalBackground;
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
        $this->authorize("isHROrAdmin");

        $civilStatus =  CivilStatus::all();
        $gender = Gender::all();

        return view("employee.create",[
            'civilStatus' => $civilStatus,
            'gender' => $gender
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
                'gender_id.required' => 'Please select a Gender',
                'civil_status_id.required' => 'Please select a Civil Status'
            ];
            //validate request value
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:50|unique:employees,name',
                'nickname' => 'required|string|max:10',
                'gender_id' => 'required|integer',
                'birthdate' => 'required|string',
                'civil_status_id' => 'required|integer',
                'address' => 'required|string|max:50',
                'contact_number' => 'required|digits:10',
                'email' => 'required|email|max:50'
            ], $messages);

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
            $employee->gender_id = $request->gender_id;
            $employee->civil_status_id = $request->civil_status_id;
            $employee->birthdate = $request->birthdate;
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

            return redirect()->route('employee.edit', $employee->id);
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

        $employee = Employee::withTrashed()->findOrFail($id);
        $employmentTypes = EmploymentType::all();
        $industries = Industry::all();
        $civilStatus =  CivilStatus::all();
        $gender = Gender::all();

        $employment_histories = $employee->employment_histories;
        $educ_backgrounds = $employee->educ_backgrounds;

        return view('employee.show', [
            'employee' => $employee,
            'employmentTypes' => $employmentTypes,
            'industries' => $industries,
            'civilStatus' => $civilStatus,
            'gender' => $gender,
            'employment_histories' => $employment_histories,
            'educ_backgrounds' => $educ_backgrounds
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

        $employee = Employee::withTrashed()->findOrFail($id);
        $employmentTypes = EmploymentType::all();
        $industries = Industry::all();
        $civilStatus =  CivilStatus::all();
        $gender = Gender::all();

        $employment_histories = $employee->employment_histories;
        $educ_backgrounds = $employee->educ_backgrounds;
   
    
        return view('employee.edit', [
            'employee' => $employee,
            'employmentTypes' => $employmentTypes,
            'industries' => $industries,
            'civilStatus' => $civilStatus,
            'gender' => $gender,
            'employment_histories' => $employment_histories,
            'educ_backgrounds' => $educ_backgrounds
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
            //check employee if exist
            $employee = Employee::withTrashed()->findOrFail($id);

            $messages = [
                'gender_id.required' => 'Please select a Gender',
                'civil_status_id.required' => 'Please select a Civil Status',
                'employment_histories.*.required' => 'Please Add atleast 1 Employment History',
                'educational_histories.*.required' => 'Please Add atleast 1 Educational Background',
                'employmentTypes.*.integer' => 'Please select Employment Type',
                'startdate.*.required' => 'Please select Start Date',
                'enddate.*.required' => 'Please select Start Date',
                'from.*.required' => 'Please select From Date',
                'to.*.required' => 'Please select To Date',
                'industries*.required' => 'Please select Nature of Work'
            ];

            //validate the request value
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|unique:employees,name,' . $employee->id,
                'nickname' => 'required|string|max:10',
                'gender_id' => 'required|integer',
                'birthdate' => 'required|string',
                'civil_status_id' => 'required|integer',
                'address' => 'required|string|max:50',
                'contact_number' => 'required|digits:10',
                'email' => 'required|email|max:50',
                'employment_histories' => 'array',
                'educational_histories' => 'array',
                'title.*' => 'required|string',
                'employmentTypes.*' => 'required|integer',
                'company.*' => 'required|string',
                'location.*' => 'required|string',
                'startdate.*' => 'required|string',
                'enddate.*' => 'required|string',
                'industries.*' => 'required|integer',
                'job_description.*' => 'required|string',
                'school_name.*' => 'required|string',
                'from.*' => 'required|string',
                'to.*' => 'required|string',
                'emergency_contact_name' => 'required|string|max:50',
                'emergency_relationship' => 'required|string|max:10',
                'emergency_address' => 'required|string|max:50',
                'emergency_contact_number' => 'required|digits:10',
                'sss' => 'required|digits:12',
                'pagibig' => 'required|digits:12',
                'philhealth' => 'required|digits:12',
                'tin' =>  'required|digits:12'
            ],$messages);
            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }

            //check current user
            $user = \Auth::user()->id;

            //save the update value
            $employee->name = $request->name;
            $employee->nickname = $request->nickname;
            $employee->gender_id = $request->gender_id;
            $employee->civil_status_id = $request->civil_status_id;
            $employee->birthdate = $request->birthdate;
            $employee->address = $request->address;
            $employee->contact_number = $request->contact_number;
            $employee->emergency_contact_name = $request->emergency_contact_name;
            $employee->emergency_relationship = $request->emergency_relationship;
            $employee->emergency_address = $request->emergency_address;
            $employee->emergency_contact_number = $request->emergency_contact_number;
            $employee->sss = $request->sss;
            $employee->pagibig = $request->pagibig;
            $employee->philhealth = $request->philhealth;
            $employee->tin = $request->tin;
            $employee->email = $request->email;
            $employee->updater_id = $user;
            $employee->update();

            $log = new Log();
            $log->log = "User " . \Auth::user()->email . " edit employee " . $employee->reference_no . " at " . Carbon::now();
            $log->creator_id =  \Auth::user()->id;
            $log->updater_id =  \Auth::user()->id;
            $log->save();

            $titles = $request->input('title', []);
            $employmentTypes = $request->input('employmentTypes', []);
            $company = $request->input('company', []);
            $location = $request->input('location', []);
            $startdate = $request->input('startdate', []);
            $enddate = $request->input('enddate', []);
            $industries = $request->input('industries', []);
            $job_description = $request->input('job_description', []);
            for ($i = 0; $i < count($titles); $i++) {
                if ($titles[$i] != '') {
                    $employment = EmploymentHistory::firstOrNew([
                        'employee_id' => $employee->id,
                    ]);
                    $employment->employment_type_id = $employmentTypes[$i];
                    $employment->title = $titles[$i] ?? '';
                    $employment->company = $company[$i] ?? '';
                    $employment->location = $location[$i] ?? '';
                    $employment->start_date = Carbon::parse($startdate[$i])->format('Y-m-d');
                    $employment->end_date = Carbon::parse($enddate[$i])->format('Y-m-d');
                    $employment->industry_id = $industries[$i] ?? '';
                    $employment->job_description = $job_description[$i] ?? '';
                    $employment->creator_id = $user;
                    $employment->updater_id = $user;
                    $employment->save();

                    $log = new Log();
                    $log->log = "User " . \Auth::user()->email . " add employment history " . $employment->title . " at " . Carbon::now();
                    $log->creator_id =  \Auth::user()->id;
                    $log->updater_id =  \Auth::user()->id;
                    $log->save();
                }
            }


            $school_names = $request->input('school_name', []);
            $from = $request->input('from', []);
            $to = $request->input('to', []);
            for ($i = 0; $i < count($school_names); $i++) {
                if ($school_names[$i] != '') {
                    $education = EducationalBackground::firstOrNew([
                        'employee_id' => $employee->id,
                    ]);
                    $education->school_name = $school_names[$i];
                    $education->from = Carbon::parse($from[$i])->format('Y-m-d');
                    $education->to = Carbon::parse($to[$i])->format('Y-m-d');
                    $education->creator_id = $user;
                    $education->updater_id = $user;
                    $education->save();

                    $log = new Log();
                    $log->log = "User " . \Auth::user()->email . " add educational background " . $education->school_name . " at " . Carbon::now();
                    $log->creator_id =  \Auth::user()->id;
                    $log->updater_id =  \Auth::user()->id;
                    $log->save();
                }
            }
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
          $this->authorize("isHROrAdmin");

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