<?php

namespace App\Http\Controllers\Salary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Deployment;
use App\Salary;
use App\Log;
use Validator, Hash, DB;
use Carbon\Carbon;

class SalaryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $payrolls = DB::table('deployments')
        ->leftJoin('attendances', 'deployments.id', '=', 'attendances.deployment_id')
        ->leftJoin('employees', 'deployments.employee_id', '=', 'employees.id')
        ->select('employees.first_name', 'attendances.attendance_date', 'attendances.hours_worked')
        ->where(function ($query) {
            $query->orWhereBetween('attendances.attendance_date', [date('Y-m-01'), date('Y-m-15')])
                ->where('attendances.deleted_at', '=', null);
        })
        ->orWhere(function ($query) {
            $query->whereBetween('attendances.attendance_date', [date('Y-m-16'), date('Y-m-t')])
            ->where('attendances.deleted_at', '=', null);
        })
        ->where([
            ['deployments.id', 1]
        ])
        ->get();
        dd($payrolls);exit();
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
        //
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
        $salary = Salary::withTrashed()->where('deployment_id', $deployment->id)->first();
        $baseRate =  [
            [ 
                'label' => 'Hourly',
                'value' => 'hourly'
            ],
            [ 
                'label' => 'Monthly',
                'value' => 'monthly'
            ]];
         return view('salary.edit', [
            'deployment' => $deployment,
            'salary' => $salary,
            'baseRate' => $baseRate
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
            $salary = Salary::withTrashed()->findOrFail($id);

            //validate request value
            $validator = Validator::make($request->all(), [
                'basic_salary' => 'required|numeric',
                'rate_base' => 'required|string|in:hourly,monthly',
                'sss' => 'required|numeric',
                'tax' => 'required|numeric',
                'tax_salary_range' => 'required|numeric',
                'thirteen_month_pay_tax' => 'required|numeric',
                'thirteen_month_pay_tax_salary_range' => 'required|numeric',
                'pagibig' => 'required|numeric',
                'philhealth' => 'required|numeric',
                'uniform' => 'required|numeric',
                'meal_allowance' => 'required|numeric',
                'transportation_allowance' => 'required|numeric',
                'cola' => 'required|numeric'
            ]);

            if ($validator->fails()) {
                return redirect()->route('workDetails',['id' => $salary->deployment_id,'parent_index' => 5])->withErrors($validator->errors())->withInput();
            }
            
            $salary->basic_salary = $request->basic_salary;
            $salary->rate_base = $request->rate_base;
            $salary->sss = $request->sss;
            $salary->tax = $request->tax;
            $salary->tax_salary_range = $request->tax_salary_range;
            $salary->thirteen_month_pay_tax = $request->thirteen_month_pay_tax;
            $salary->thirteen_month_pay_tax_salary_range = $request->thirteen_month_pay_tax_salary_range;
            $salary->pagibig = $request->pagibig;
            $salary->philhealth = $request->philhealth;
            $salary->uniform = $request->uniform;
            $salary->meal_allowance = $request->meal_allowance;
            $salary->laundry_allowance = $request->laundry_allowance;
            $salary->transportation_allowance = $request->transportation_allowance;
            $salary->cola = $request->cola;
            $salary->updater_id = \Auth::user()->id;
            $salary->save();

            $log = new Log();
            $log->log = "User " . \Auth::user()->email . " edit salary " .  $salary->id . " at " . Carbon::now();
            $log->creator_id =  \Auth::user()->id;
            $log->updater_id =  \Auth::user()->id;
            $log->save();

            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();

            return redirect()->route('workDetails',['id' => $salary->deployment_id,'parent_index' => 5])
                ->with('successMsg', 'Salary Data update Successfully');
        } catch (\Exception $e) {
            \DB::rollback();
            return redirect()->route('workDetails',['id' => $salary->deployment_id,'parent_index' => 5])->withErrors($e->getMessage());
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
        //
    }
}
