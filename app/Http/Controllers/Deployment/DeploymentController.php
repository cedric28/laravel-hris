<?php

namespace App\Http\Controllers\Deployment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Deployment;
use App\Client;
use App\Employee;
use App\EmploymentType;
use App\Log;
use App\Role;
use App\Salary;
use App\Schedule;
use App\LateTime;
use App\OverTime;
use App\Industry;
use App\CivilStatus;
use App\Gender;
use App\LeaveType;
use Validator, Hash, DB;
use Carbon\Carbon;

class DeploymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $deployments = Deployment::all();
        $InactiveDeployment = Deployment::onlyTrashed()->get();

        return view("deployment.index", [
            'deployments' => $deployments,
            'InactiveDeployment' => $InactiveDeployment
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

        $clients = Client::all();
        $employees = Employee::whereDoesntHave('deployments', function ($query) {
            $query->where('status', 'new');
            })->get();
        $employmentTypes = EmploymentType::all();

        return view("deployment.create",[
            'clients' => $clients,
            'employees' => $employees,
            'employmentTypes' => $employmentTypes
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
                'employment_type_id.required' => 'Please select a Employment Type',
                'position.required' => 'Please enter a Position',
                'position.max' => 'Position must not be more than 50 characters',
                'start_date.required' => 'Please enter a Start Date',
            ];
            //validate request value
            $validator = Validator::make($request->all(), [
                'employee_id' => [
                    'required',
                    'integer',
                    function ($attribute, $value, $fail) use ($request) {
                        $attendance = Deployment::where('employee_id', $value)
                        ->where('status', 'new')
                        ->exists();
            
                        if ($attendance) {
                            $fail('Employee has an active client');
                        }
                    },
                ],
                'client_id' => 'required|integer',
                'employment_type_id' => 'required|integer',
                'position' => 'required|string|max:50',
                'start_date' => 'required|string',
                // 'end_date' => [
                //     'required',
                //     'string',
                //     'after:start_date',
                //     function ($attribute, $value, $fail) use ($request) {
                //         if ($request->start_date) {
                //             $startDate = Carbon::createFromFormat('m/d/Y', $request->start_date)->startOfDay();
                //             $endDate = Carbon::createFromFormat('m/d/Y', $value)->startOfDay();

                //              // Check if the end date falls on a weekend
                //             if ($endDate->isWeekend()) {
                //                 // Move the end date to the next weekday
                //                 $endDate = $endDate->next(Carbon::MONDAY);
                //             }

                //             if ($startDate->diffInMonths($endDate) !== 12 || !$endDate->eq($startDate->copy()->addYear())) {
                //                 $fail('End Date must be exactly one year after Start Date');
                //             }
                //         }
                //     },
                // ],
            ], $messages);



            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }

            //check current user
            $user = \Auth::user()->id;
           
            $startDate = Carbon::parse($request->start_date);
           
            $oneYearLater = $startDate->addYear();
            $oneYearLaterFormatted = $oneYearLater->toDateString();

            //save deployment
            $deployment = new Deployment();
            $deployment->reference_no = $this->generateUniqueCode();
            $deployment->employee_id = $request->employee_id;
            $deployment->employment_type_id = $request->employment_type_id;
            $deployment->client_id = $request->client_id;
            $deployment->position = $request->position;
            $deployment->start_date = Carbon::parse($request->start_date)->format('Y-m-d');
            $deployment->end_date = $oneYearLaterFormatted;
            $deployment->creator_id = $user;
            $deployment->updater_id = $user;
            $deployment->save();
            
            $salary = new Salary();
            $salary->deployment_id = $deployment->id;
            $salary->creator_id = $user;
            $salary->updater_id = $user;
            $salary->save();


            $schedule = new Schedule();
            $schedule->deployment_id =  $deployment->id;
            $schedule->slug = 'Monday - Friday';
            $schedule->time_in = Carbon::parse('07:00:00')->format('H:i:s');
            $schedule->time_out = Carbon::parse('16:00:00')->format('H:i:s');
            $schedule->creator_id = $user;
            $schedule->updater_id = $user;
            $schedule->save();

            $log = new Log();
            $log->log = "User " . \Auth::user()->email . " create Employee Work Details" . $deployment->reference_no . " at " . Carbon::now();
            $log->creator_id =  \Auth::user()->id;
            $log->updater_id =  \Auth::user()->id;
            $log->save();
            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();

            return redirect()->route('workDetails',['id' => $deployment->id,'parent_index' => 1]);
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

        $deployment = Deployment::withTrashed()->findOrFail($id);

        return view('deployment.show', [
            'deployment' => $deployment
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

        $deployment = Deployment::withTrashed()->findOrFail($id);
        $employee = Employee::withTrashed()->findOrFail($deployment->employee_id);
        $industries = Industry::all();
        $civilStatus =  CivilStatus::all();
        $gender = Gender::all();

        $employment_histories = $employee->employment_histories;
        $educ_backgrounds = $employee->educ_backgrounds;


        $educationLevel = [ 
            [ 
                'name' => 'Primary'
            ],
            [
                'name' => 'Secondary'
            ],
            [
                'name' => 'Tertiary'
            ], 
            [
                'name' => 'Vocational'
            ]
        ];


        $employmentTypes = EmploymentType::all();
        $clients = Client::all();
        $employees = Employee::all();

        $statuses = [ 
            [ 
                'name' => 'new'
            ],
            [
                'name' => 'regular'
            ],
            [
                'name' => 'end'
            ],
            [
                'name' => 'terminate'
            ]
            ];
   
        return view('deployment.edit', [
            'clients' => $clients,
            'employees' => $employees,
            'employmentTypes' => $employmentTypes,
            'deployment' => $deployment,
            'statuses' => $statuses,
            'employee' => $employee,
            'industries' => $industries,
            'civilStatus' => $civilStatus,
            'gender' => $gender,
            'employment_histories' => $employment_histories,
            'educ_backgrounds' => $educ_backgrounds,
            'educationLevel' => $educationLevel
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
                'client_id.required' => 'Please select a Client',
                'employment_type_id.required' => 'Please select a Employment Type',
                'end_date.after' => 'End Date must be after Start Date',
            ];
            //validate request value
            $validator = Validator::make($request->all(), [
                'client_id' => 'required|integer',
                'employment_type_id' => 'required|integer',
                'position' => 'required|string|max:50',
                'start_date' => 'required|string|max:50',
                'status' => 'required|string',
                // 'end_date' => [
                //     'required',
                //     'string',
                //     'after:start_date',
                //     function ($attribute, $value, $fail) use ($request) {
                //         if ($request->start_date) {
                //             $startDate = Carbon::createFromFormat('m/d/Y', $request->start_date)->startOfDay();
                //             $endDate = Carbon::createFromFormat('m/d/Y', $value)->startOfDay();

                //              // Check if the end date falls on a weekend
                //             if ($endDate->isWeekend()) {
                //                 // Move the end date to the next weekday
                //                 $endDate = $endDate->next(Carbon::MONDAY);
                //             }

                //             if ($startDate->diffInMonths($endDate) !== 12 || !$endDate->eq($startDate->copy()->addYear())) {
                //                 $fail('End Date must be exactly one year after Start Date');
                //             }
                //         }
                //     },
                // ],
            ], $messages);

            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }

            $startDate = Carbon::parse($request->start_date);

            $oneYearLater = $startDate->addYear();
            $oneYearLaterFormatted = $oneYearLater->toDateString();

            $deployment->employment_type_id = $request->employment_type_id;
            $deployment->client_id = $request->client_id;
            $deployment->position = $request->position;
            $deployment->status = $request->status;
            $deployment->start_date = Carbon::parse($request->start_date)->format('Y-m-d');
            $deployment->end_date = $oneYearLaterFormatted;
            $deployment->updater_id = \Auth::user()->id;
            $deployment->save();

            $log = new Log();
            $log->log = "User " . \Auth::user()->email . " edit Employee Work Details" .   $deployment->reference_no . " at " . Carbon::now();
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

    public function workDetails($id)
    {
        //prevent other user to access to this page
        $this->authorize("isHROrAdmin");

        $deployment = Deployment::withTrashed()->findOrFail($id);
        $schedule = Schedule::withTrashed()->where('deployment_id', $deployment->id)->first();
        $lates = LateTime::withTrashed()->where('deployment_id', $deployment->id)->get();
        $overtimes = OverTime::withTrashed()->where('deployment_id', $deployment->id)->get();
        $leaveTypes = LeaveType::all();
        $salary = Salary::withTrashed()->where('deployment_id', $deployment->id)->first();
        $baseRate =  [
            [ 
                'label' => 'Hourly',
                'value' => 'hourly'
            ]];

        $birthdate = Carbon::createFromFormat('Y-m-d', $deployment->employee->birthdate);

        $age = $birthdate->diffInYears();
        return view('deployment.work-details', [
           'deployment' => $deployment,
           'schedule'=> $schedule,
           'lates' => $lates,
           'overtimes' => $overtimes,
           'leaveTypes' => $leaveTypes,
           'salary' => $salary,
           'baseRate' => $baseRate,
           'age' => $age
        ]);
    }

    public function generateUniqueCode()
    {
        do {
            $year = Carbon::now()->year;
            $reference_no = $year.random_int(1000000000, 9999999999);
        } while (Deployment::where("reference_no", "=", $reference_no)->first());

        return $reference_no;
    }
}
