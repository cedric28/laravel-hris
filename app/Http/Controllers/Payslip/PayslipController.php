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
use App\GeneralDeduction;
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
        //prevent other user to access to this page
        $this->authorize("isHROrAdmin");
        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();
  
        try {
            //validate request value
            $validator = Validator::make($request->all(), [
                'deployment_id' => 'required|integer',
                'payroll_id' => [
                    'required',
                    'integer',
                    function ($attribute, $value, $fail) use ($request) {
                        $payroll = Payroll::find($request->payroll_id);

                        if(!$payroll) {
                            $fail('Payroll not found');
                        } 
                    },
                ],
                'other_pay' => 'required|numeric',
                'other_deduction' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return redirect()->route('workDetails',['id' => $request->deployment_id,'parent_index' => 6])->withErrors($validator->errors())->withInput();
            }

            //check current user
            $user = \Auth::user()->id;

            $deploymentId = $request->deployment_id;
            $payrollId = $request->payroll_id;
            $include_13th_month = $request->include_13th_month ?? false;
            $payroll = Payroll::find($payrollId);

            $startDate = Carbon::parse($payroll->start_date)->format('Y-m-d');
            $endDate =  Carbon::parse($payroll->end_date)->format('Y-m-d');
            $year =  Carbon::parse($payroll->end_date)->format('Y');

            if ($request->include_13th_month) {
                $existingPayslip = Payslip::whereHas('payroll', function ($query) use ($year) {
                        $query->whereYear('end_date', $year);
                    })
                    ->where('include_thirteen_month_pay', 1)
                    ->first();

                if ($existingPayslip) {
                    return redirect()->route('workDetails',['id' => $request->deployment_id,'parent_index' => 6])->with('errorMsg','Cannot include 13th month pay because a payslip with 13th month pay has already been created for this year. If you want to update the 13th month pay record, please modify the existing payslip first.');
                }
            }

            $attendance = Attendance::where('deployment_id', $deploymentId)
            ->where('status', 'Present')
            ->whereNull('deleted_at')
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->get();
    
            $employeeDetails = Deployment::findOrFail($deploymentId);
        
            $totalHoursWorked = Attendance::where('deployment_id', $deploymentId)
                ->where('status', 'Present')
                ->whereNull('deleted_at')
                ->whereBetween('attendance_date', [$startDate, $endDate])
                ->sum('hours_worked');

            if($totalHoursWorked <= 0){
                return redirect()->route('workDetails',['id' => $request->deployment_id,'parent_index' => 6])->with('errorMsg', 'Create Attendance first');
            }
        
            $totalHoursOverTime = OverTime::where('deployment_id', $deploymentId)
                ->whereNull('deleted_at')
                ->whereBetween('overtime_date', [$startDate, $endDate])
                ->sum(DB::raw('TIME_TO_SEC(duration) / 60'));
        
            $totalHoursLate = LateTime::where('deployment_id', $deploymentId)
                ->whereNull('deleted_at')
                ->whereBetween('latetime_date', [$startDate, $endDate])
                ->sum(DB::raw('TIME_TO_SEC(duration) / 60'));
        
            // Compute salary details
            $basicSalary =  $employeeDetails->salary->basic_salary ?? 0;
            $ratePerHour = ($basicSalary / 8);
            $basicSalaryTotal = floatval($ratePerHour) * floatval($totalHoursWorked);
            $overTimeTotal = ($totalHoursOverTime / 60) * ($ratePerHour * 1.25);
            $lateTotalDeduction = (($totalHoursLate / 60) * $ratePerHour) ?? 0;
    
            // Get holidays within the payroll date range
            $holidays = HolidaySetting::whereBetween('holiday', [$startDate, $endDate])
                ->whereNull('deleted_at')
                ->get();
    
            $totalHolidayPay = 0;
    
            foreach ($holidays as $holiday) {
                $holidayDate = Carbon::parse($holiday->holiday)->format('Y-m-d');
    
                // Calculate previous, exact, and next day dates
                $previousDay = Carbon::parse($holidayDate)->subDay()->format('Y-m-d');
                $nextDay = Carbon::parse($holidayDate)->addDay()->format('Y-m-d');
                
                // Check if previous or next day is a weekend
                $isPreviousDayWeekend = Carbon::parse($previousDay)->isWeekend();
                $isNextDayWeekend = Carbon::parse($nextDay)->isWeekend();
    
                // Find attendance on the day before, exact holiday, and the day after
                $previousDayAttendance = Attendance::where('deployment_id', $deploymentId)
                    ->where('status', 'Present')
                    ->whereNull('deleted_at')
                    ->where('attendance_date', $previousDay)
                    ->first();
    
                $holidayAttendance = Attendance::where('deployment_id', $deploymentId)
                    ->where('status', 'Present')
                    ->whereNull('deleted_at')
                    ->where('attendance_date', $holidayDate)
                    ->first();
    
                $nextDayAttendance = Attendance::where('deployment_id', $deploymentId)
                    ->where('status', 'Present')
                    ->whereNull('deleted_at')
                    ->where('attendance_date', $nextDay)
                    ->first();
    
                // Check if the previous or next day is a weekend without attendance records
                if ($isPreviousDayWeekend && !$previousDayAttendance) {
                    $previousDayAttendance = (object) ['hours_worked' => 0]; // Create a dummy attendance object
                }
    
                if ($isNextDayWeekend && !$nextDayAttendance) {
                    $nextDayAttendance = (object) ['hours_worked' => 0]; // Create a dummy attendance object
                }
    
                // If any of the attendance records exist, calculate holiday pay
                if ($previousDayAttendance && $holidayAttendance && $nextDayAttendance) {
                    $relevantAttendance = $holidayAttendance ?: ($previousDayAttendance ?: $nextDayAttendance); // Priority: exact holiday, before, then after
    
                    // Calculate overtime during the holiday
                    $totalHoursOverTimeHoliday = OverTime::where('deployment_id', $deploymentId)
                        ->whereNull('deleted_at')
                        ->where('overtime_date', $holidayDate)
                        ->sum(DB::raw('TIME_TO_SEC(duration) / 60'));
    
                    // Calculate holiday pay based on attendance
                    $overTimeTotalHoliday = ($totalHoursOverTimeHoliday / 60) * (($basicSalary / 8) * $holiday->percentage);
                    $holidayAmount = ($relevantAttendance->hours_worked * $ratePerHour) * $holiday->percentage;
                    $holidayAmount += $overTimeTotalHoliday;
    
                    // Add the holiday pay to the total
                    $totalHolidayPay += $holidayAmount;
                }
            }

            $generalDeductions = GeneralDeduction::all();
            $totalGeneralDeductions = 0;
            // Check if generalDeductions is not empty
            if ($generalDeductions->isNotEmpty()) {
              // Get all the amount values, divide by 2, and sum the results
              $totalGeneralDeductions = $generalDeductions->sum(function ($deduction) {
                  return $deduction->amount / 2;
              });
            } else {
              $totalGeneralDeductions = 0; // Set to 0 if there are no deductions
            }
            
            // Additional pay and deductions
            $deMinimisBenefits = ($employeeDetails->salary->meal_allowance ?? 0 ) + ($employeeDetails->salary->laundry_allowance ?? 0 ) + ($employeeDetails->salary->transportation_allowance ?? 0 ) + ($employeeDetails->salary->cola ?? 0 );
            $totalCompensation = floatval($basicSalaryTotal) + $deMinimisBenefits + floatval($overTimeTotal) + (floatval($request->other_pay) ?? 0) + floatval($totalHolidayPay);

            // ===== START: 13th Month Pay Computation =====
            $endDateYear = Carbon::parse($endDate)->year;
            $deploymentStartDate = Carbon::parse($employeeDetails->start_date);

            // Check if deployment start date is in the same year as end date
            if ($deploymentStartDate->year !== $endDateYear) {
                $startFor13thMonth = Carbon::create($endDateYear, 1, 1); // January 1 of endDate's year
            } else {
                $startFor13thMonth = $deploymentStartDate;
            }

            // Calculate total days worked (Present only) within the given range
            $totalWorkedDaysFor13thMonth = Attendance::where('deployment_id', $deploymentId)
            ->where('status', 'Present')
            ->whereNull('deleted_at')
            ->whereBetween('attendance_date', [$startFor13thMonth->format('Y-m-d'), $endDate])
            ->count(); // count = total days worked
        
            $basicSalary = $employeeDetails->salary->basic_salary ?? 0;
            $dailySalary = $basicSalary; // As per your given logic: fixed daily salary

            $totalBasicSalaryEarned = $dailySalary * $totalWorkedDaysFor13thMonth;
            $thirteenthMonthPay = $totalBasicSalaryEarned / 12;
        
            $thirteenthMonthTaxableThreshold =  $employeeDetails->salary->thirteen_month_pay_tax_salary_range;
            $thirteenthMonthTax = 0;
        
            if ($thirteenthMonthPay > $thirteenthMonthTaxableThreshold) {
                $thirteenthMonthTax = ($thirteenthMonthPay - $thirteenthMonthTaxableThreshold) * ($employeeDetails->salary->thirteen_month_pay_tax ?? 0);
            }
            $totalCompensationWith13thMonthPay = $include_13th_month ? $totalCompensation + $thirteenthMonthPay :  $totalCompensation;
            $netPay =  $totalCompensationWith13thMonthPay - $totalDeduction;
            $tax = $totalCompensation >= $employeeDetails->salary->tax_salary_range ? $totalCompensation / $employeeDetails->salary->tax ?? 0 : 0;
            // ===== END: 13th Month Pay Computation =====
            $totalDeduction =
                ($employeeDetails->salary->sss / 2 ?? 0) +
                ($employeeDetails->salary->philhealth / 2 ?? 0) +
                ($employeeDetails->salary->pagibig / 2 ?? 0) +
                ($employeeDetails->salary->uniform ?? 0) +
                ($tax ?? 0) +
                ($lateTotalDeduction ?? 0) +
                ($request->other_deduction ?? 0) +
                $totalGeneralDeductions +
                $thirteenthMonthTax; // Optional: include 13th month tax in deductions

            $payslip = Payslip::firstOrNew(
                ['deployment_id' => $deploymentId, 'payroll_id' => $payrollId]
            );

            // If the record exists, update fields, otherwise, it will create a new one
            $payslip->holiday_pay = $totalHolidayPay;
            $payslip->other_deduction = $request->other_deduction;
            $payslip->include_thirteen_month_pay = $include_13th_month ? 1 : 0;
            $payslip->thirteen_month_pay_non_taxable = $thirteenthMonthPay;
            $payslip->thirteen_month_pay_taxable = $thirteenthMonthTax;
            $payslip->other_pay = $request->other_pay;
            $payslip->creator_id = \Auth::user()->id;
            $payslip->updater_id = \Auth::user()->id;

            // Save the payslip data (either updating or creating)
            $payslip->save();
    

            $log = new Log();
            $log->log = "User " . \Auth::user()->email . " create payslip" . $payslip->id . " at " . Carbon::now();
            $log->creator_id =  \Auth::user()->id;
            $log->updater_id =  \Auth::user()->id;
            $log->save();
            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();

            return redirect()->route('workDetails',['id' => $deploymentId,'parent_index' => 6])->with('successMsg', 'Successfully create payslip for '.$payroll->description);
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
        $include_13th_month = $request->include_13th_month ?? false;
        $payroll = Payroll::find($payrollId);

        if (!$payroll) {
            return response()->json(['error' => 'Payroll not found'], 404);
        }

        $startDate = Carbon::parse($payroll->start_date)->format('Y-m-d');
        $endDate = Carbon::parse($payroll->end_date)->format('Y-m-d');

        $attendance = Attendance::where('deployment_id', $deploymentId)
            ->where('status', 'Present')
            ->whereNull('deleted_at')
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->get();
    
        $employeeDetails = Deployment::findOrFail($deploymentId);
    
        $totalHoursWorked = Attendance::where('deployment_id', $deploymentId)
            ->where('status', 'Present')
            ->whereNull('deleted_at')
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->sum('hours_worked');
    
        if ($totalHoursWorked <= 0) {
            return response()->json(['error' => 'Please create attendance first'], 404);
        }
    
        $totalHoursOverTime = OverTime::where('deployment_id', $deploymentId)
            ->whereNull('deleted_at')
            ->whereBetween('overtime_date', [$startDate, $endDate])
            ->sum(DB::raw('TIME_TO_SEC(duration) / 60'));
    
        $totalHoursLate = LateTime::where('deployment_id', $deploymentId)
            ->whereNull('deleted_at')
            ->whereBetween('latetime_date', [$startDate, $endDate])
            ->sum(DB::raw('TIME_TO_SEC(duration) / 60'));
    
        $basicSalary = $employeeDetails->salary->basic_salary ?? 0;
        $ratePerHour = ($basicSalary / 8);
        $basicSalaryTotal = floatval($ratePerHour) * floatval($totalHoursWorked);
        $overTimeTotal = ($totalHoursOverTime / 60) * ($ratePerHour * 1.25);
        $lateTotalDeduction = (($totalHoursLate / 60) * $ratePerHour) ?? 0;
    
        $holidays = HolidaySetting::whereBetween('holiday', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->get();
    
        $totalHolidayPay = 0;
    
        foreach ($holidays as $holiday) {
            $holidayDate = Carbon::parse($holiday->holiday)->format('Y-m-d');
            $previousDay = Carbon::parse($holidayDate)->subDay()->format('Y-m-d');
            $nextDay = Carbon::parse($holidayDate)->addDay()->format('Y-m-d');
            $isPreviousDayWeekend = Carbon::parse($previousDay)->isWeekend();
            $isNextDayWeekend = Carbon::parse($nextDay)->isWeekend();
    
            $previousDayAttendance = Attendance::where('deployment_id', $deploymentId)
                ->where('status', 'Present')
                ->whereNull('deleted_at')
                ->where('attendance_date', $previousDay)
                ->first();
    
            $holidayAttendance = Attendance::where('deployment_id', $deploymentId)
                ->where('status', 'Present')
                ->whereNull('deleted_at')
                ->where('attendance_date', $holidayDate)
                ->first();
    
            $nextDayAttendance = Attendance::where('deployment_id', $deploymentId)
                ->where('status', 'Present')
                ->whereNull('deleted_at')
                ->where('attendance_date', $nextDay)
                ->first();
    
            if ($isPreviousDayWeekend && !$previousDayAttendance) {
                $previousDayAttendance = (object) ['hours_worked' => 0];
            }
    
            if ($isNextDayWeekend && !$nextDayAttendance) {
                $nextDayAttendance = (object) ['hours_worked' => 0];
            }
    
            if ($previousDayAttendance && $holidayAttendance && $nextDayAttendance) {
                $relevantAttendance = $holidayAttendance ?: ($previousDayAttendance ?: $nextDayAttendance);
    
                $totalHoursOverTimeHoliday = OverTime::where('deployment_id', $deploymentId)
                    ->whereNull('deleted_at')
                    ->where('overtime_date', $holidayDate)
                    ->sum(DB::raw('TIME_TO_SEC(duration) / 60'));
    
                $overTimeTotalHoliday = ($totalHoursOverTimeHoliday / 60) * (($basicSalary / 8) * $holiday->percentage);
                $holidayAmount = ($relevantAttendance->hours_worked * $ratePerHour) * $holiday->percentage;
                $holidayAmount += $overTimeTotalHoliday;
    
                $totalHolidayPay += $holidayAmount;
            }
        }
    
        $generalDeductions = GeneralDeduction::all();
        $totalGeneralDeductions = 0;
        if ($generalDeductions->isNotEmpty()) {
            $totalGeneralDeductions = $generalDeductions->sum(function ($deduction) {
                return $deduction->amount / 2;
            });
        }
    
        $deMinimisBenefits = 
            ($employeeDetails->salary->meal_allowance ?? 0) +
            ($employeeDetails->salary->laundry_allowance ?? 0) +
            ($employeeDetails->salary->transportation_allowance ?? 0) +
            ($employeeDetails->salary->cola ?? 0);
    
        $totalCompensation = floatval($basicSalaryTotal) + $deMinimisBenefits + floatval($overTimeTotal) + (floatval($request->other_pay) ?? 0) + floatval($totalHolidayPay);
    
        $tax = $totalCompensation >= $employeeDetails->salary->tax_salary_range
            ? $totalCompensation / ($employeeDetails->salary->tax ?? 0)
            : 0;
    
        // ===== START: 13th Month Pay Computation =====
        $endDateYear = Carbon::parse($endDate)->year;
        $deploymentStartDate = Carbon::parse($employeeDetails->start_date);

        // Check if deployment start date is in the same year as end date
        if ($deploymentStartDate->year !== $endDateYear) {
            $startFor13thMonth = Carbon::create($endDateYear, 1, 1); // January 1 of endDate's year
        } else {
            $startFor13thMonth = $deploymentStartDate;
        }

        // Calculate total days worked (Present only) within the given range
        $totalWorkedDaysFor13thMonth = Attendance::where('deployment_id', $deploymentId)
        ->where('status', 'Present')
        ->whereNull('deleted_at')
        ->whereBetween('attendance_date', [$startFor13thMonth->format('Y-m-d'), $endDate])
        ->count(); // count = total days worked
    
        $basicSalary = $employeeDetails->salary->basic_salary ?? 0;
        $dailySalary = $basicSalary; // As per your given logic: fixed daily salary

        $totalBasicSalaryEarned = $dailySalary * $totalWorkedDaysFor13thMonth;
        $thirteenthMonthPay = $totalBasicSalaryEarned / 12;
    
        $thirteenthMonthTaxableThreshold =  $employeeDetails->salary->thirteen_month_pay_tax_salary_range;
        $thirteenthMonthTax = 0;
    
        if ($thirteenthMonthPay > $thirteenthMonthTaxableThreshold) {
            $thirteenthMonthTax = ($thirteenthMonthPay - $thirteenthMonthTaxableThreshold) * ($employeeDetails->salary->thirteen_month_pay_tax ?? 0);
        }

        // ===== END: 13th Month Pay Computation =====
        $totalDeduction = 
            ($employeeDetails->salary->sss / 2 ?? 0) +
            ($employeeDetails->salary->philhealth / 2 ?? 0) +
            ($employeeDetails->salary->pagibig / 2 ?? 0) +
            ($employeeDetails->salary->uniform ?? 0) +
            ($tax ?? 0) +
            ($lateTotalDeduction ?? 0) +
            ($request->other_deduction ?? 0) +
            $totalGeneralDeductions +
            $thirteenthMonthTax; // Optional: include 13th month tax in deductions

        $totalCompensation = $include_13th_month ? $totalCompensation + $thirteenthMonthPay :  $totalCompensation;
        $netPay = $totalCompensation - $totalDeduction;

        return response()->json([
            'totalHoursWorked' => $totalHoursWorked,
            'totalHoursOverTime' => $totalHoursOverTime,
            'totalHoursLate' => $totalHoursLate,
            'ratePerHour' => $ratePerHour,
            'overTimeTotal' => $overTimeTotal,
            'totalCompensation' => "P " . number_format($totalCompensation, 2),
            'totalLateDeduction' => $lateTotalDeduction,
            'totalDeduction' => "P " . number_format($totalDeduction, 2),
            'tax' => "P " . number_format($tax, 2),
            'basicSalaryTotal' => $basicSalaryTotal,
            'totalHolidayPay' => "P " . number_format($totalHolidayPay, 2),
            'netPay' => "P " . number_format($netPay, 2),
            'thirteenthMonthPay' => "P " . number_format($thirteenthMonthPay, 2),
            'thirteenthMonthTax' => "P " . number_format($thirteenthMonthTax, 2)
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
