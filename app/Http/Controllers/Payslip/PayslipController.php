<?php

namespace App\Http\Controllers\Payslip;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Log;
use App\Payslip;
use App\User;
use App\Attendance;
use Carbon\Carbon;
use App\OverTime;
use App\LateTime;
use App\Payroll;
use App\Deployment;
use App\HolidaySetting;
use Validator, Hash, DB;

class PayslipController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return abort(404);
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
        return view("payslip-setting.create");
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
        $user = User::findOrFail($id);
        return view("payslip-setting.show");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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


    public function checkWorkDetails(Request $request)
    {
        $deploymentId = $request->deployment_id;
        $payrollId = $request->payroll_id;
        $payroll = Payroll::find($payrollId);
        
        if (!$payroll) {
            return response()->json(['error' => 'Payroll not found'], 404);
        }
        
        $startDate = Carbon::parse($payroll->start_date)->format('Y-m-d');
        $endDate =  Carbon::parse($payroll->end_date)->format('Y-m-d');

        $attendance = Attendance::where('deployment_id', $deploymentId)
        ->where('status', 'Present')
        ->whereBetween('attendance_date', [$startDate, $endDate])
        ->get();

        $employeeDetails = Deployment::findOrFail($deploymentId);
    
        $totalHoursWorked = Attendance::where('deployment_id', $deploymentId)
            ->where('status', 'Present')
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->sum('hours_worked');
    
        $totalHoursOverTime = OverTime::where('deployment_id', $deploymentId)
            ->whereBetween('overtime_date', [$startDate, $endDate])
            ->sum(DB::raw('TIME_TO_SEC(duration) / 60'));
    
        $totalHoursLate = LateTime::where('deployment_id', $deploymentId)
            ->whereBetween('latetime_date', [$startDate, $endDate])
            ->sum(DB::raw('TIME_TO_SEC(duration) / 60'));
    
        // Compute salary details
        $basicSalary =  $employeeDetails->salary->basic_salary ?? 0;
        $ratePerHour = ($basicSalary / 8);
        $basicSalaryTotal = floatval($ratePerHour) * floatval($totalHoursWorked);
        $overTimeTotal = ($totalHoursOverTime / 60) * ($ratePerHour * 1.25);
        $lateTotalDeduction = (($totalHoursLate / 60) * $ratePerHour) ?? 0;

        // Get holidays within the payroll date range
        $holidays = HolidaySetting::whereBetween('holiday', [$startDate, $endDate])->get();
        $totalHolidayPay = 0;
        foreach ($attendance as $attend) {
            $attendanceDate = Carbon::parse($attend->attendance_date)->format('Y-m-d');

            foreach ($holidays as $holiday) {
                $holidayDate = Carbon::parse($holiday->holiday)->format('Y-m-d');
                
                // Check if attendance date falls before or after the holiday
                if ($attendanceDate === $holidayDate) {
                    // Adjust the attendance dates around the holiday (example logic)
                    $totalHoursOverTimeHoliday = OverTime::where('deployment_id',  $deploymentId)
                    ->where('overtime_date',$attendanceDate)
                    ->sum(DB::raw('TIME_TO_SEC(duration) / 60'));
                    $overTimeTotalHoliday = (($totalHoursOverTime / 60) * (($basicSalary / 8 ) * $holiday->percentage));
                    $holidayAmount = ($attend->hours_worked * $ratePerHour) * $holiday->percentage;
                    $holidayAmount = $holidayAmount + $overTimeTotalHoliday;
                    $totalHolidayPay = $holidayAmount;
                }
            }
        }
    
        // Additional pay and deductions
        $deMinimisBenefits = ($employeeDetails->salary->meal_allowance ?? 0 ) + ($employeeDetails->salary->laundry_allowance ?? 0 ) + ($employeeDetails->salary->transportation_allowance ?? 0 ) + ($employeeDetails->salary->cola ?? 0 );
        $totalCompensation = floatval($basicSalaryTotal) + $deMinimisBenefits + floatval($overTimeTotal) + (floatval($request->other_pay) ?? 0) + floatval($totalHolidayPay);
        $totalDeduction = ($employeeDetails->salary->sss ?? 0) + 
                          ($employeeDetails->salary->philhealth ?? 0) +
                          ($employeeDetails->salary->pagibig ?? 0) + 
                          ($employeeDetails->salary->uniform ?? 0) +
                          ($tax ?? 0) + 
                          ($lateTotalDeduction ?? 0) +
                          ($request->other_deduction ?? 0);
    
        $netPay =  $totalCompensation - $totalDeduction;

        $tax = $totalCompensation >= 21000 ? $totalCompensation / $employeeDetails->salary->tax ?? 0 : 0;
    
        return response()->json([
            'totalHoursWorked' => $totalHoursWorked,
            'totalHoursOverTime' => $totalHoursOverTime,
            'totalHoursLate' => $totalHoursLate,
            'ratePerHour' => $ratePerHour,
            'overTimeTotal' => $overTimeTotal,
            'totalCompensation' => "P ".number_format($totalCompensation,2),
            'totalLateDeduction' => $lateTotalDeduction,
            'totalDeduction' => "P ".number_format($totalDeduction,2),
            'tax' => "P ".number_format($tax,2),
            'basicSalaryTotal' => $basicSalaryTotal,
            'totalHolidayPay' =>  "P ".number_format($totalHolidayPay,2),
            'netPay' =>  "P ".number_format($netPay,2)
        ]);
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

        //delete category
        $payslip = Payslip::findOrFail($id);
        $payslip->delete();

        $log = new Log();
        $log->log = "User " . \Auth::user()->email . " delete payslip " . $payslip->id . " at " . Carbon::now();
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

            $payslip = Payslip::onlyTrashed()->findOrFail($id);

            /* Restore payslip */
            $payslip->restore();

            $log = new Log();
            $log->log = "User " . \Auth::user()->email . " restore payslip " . $payslip->id . " at " . Carbon::now();
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
