<?php

namespace App\Http\Controllers\PDF;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Log;
use App\Feedback;
use App\Deployment;
use App\Attendance;
use App\OverTime;
use App\LateTime;
use App\Payslip;
use App\Payroll;
use App\HolidaySetting;
use App\GeneralDeduction;
use Carbon\Carbon;
use PDF, DB;

class PDFController extends Controller
{
  public function generatePerfectAttendance(Request $request,$id)
  { 
    set_time_limit(300);
    $employeeDetails = Deployment::findOrFail($id);
    $employee = ucwords($employeeDetails->employee->last_name).", ".ucwords($employeeDetails->employee->first_name)." ".ucwords($employeeDetails->employee->middle_name);
    $company = ucwords($employeeDetails->client->name);
    
    $now = Carbon::now();
    $year =  $now->year;
    $month =  $now->translatedFormat('F');
    $day =  $now->format('jS');
    //check current user
    $user = \Auth::user();

    $pdf = \PDF::loadView('pdf.perfect_attendance',[
        'company'=>  $company,
        'employee'=>$employee,
        'month' => $month,
        'year' => $year,
        'day' =>$day
    ]);

    $pdf->setPaper('a4', 'landscape');

    $log = new Log();
    $log->log = "User " . \Auth::user()->email . " generate Perfect Attendance at " . Carbon::now();
    $log->creator_id =  \Auth::user()->id;
    $log->updater_id =  \Auth::user()->id;
    $log->save();

    return $pdf->stream("Perfect-Attendance-" .  $month."-". $year."-".$employee. ".pdf");
  }

  public function generateForRegularization(Request $request,$id)
  {
    set_time_limit(300);
    $now = Carbon::now();
    $yearNow =  $now->year;
    $currentMonth = Carbon::now()->month;
  
    //check current user
    $user = \Auth::user();
    $employeeDetails = Feedback::findOrFail($id);
    $employee = ucwords($employeeDetails->deployment->employee->last_name).", ".ucwords($employeeDetails->deployment->employee->first_name)." ".ucwords($employeeDetails->deployment->employee->middle_name);
    $company = ucwords($employeeDetails->deployment->client->name);

    // return view("pdf.for_regularization");
    // view()->share('for_regularization', $user);
    $pdf = \PDF::loadView('pdf.for_regularization',[
       'company'=>  $company,
       'employee'=>$employee
    ]);

    $pdf->setPaper('a4', 'landscape');


    $log = new Log();
    $log->log = "User " . \Auth::user()->email . " generate PDF For Regularization at " . Carbon::now();
    $log->creator_id =  \Auth::user()->id;
    $log->updater_id =  \Auth::user()->id;
    $log->save();

    return $pdf->stream("Regularization-" .  $employee . ".pdf");
  }

  public function generateBestPerformer(Request $request,$id)
  {
    set_time_limit(300);
    $now = Carbon::now();
    $year =  $now->year;
    $month =  $now->translatedFormat('F');
    $day =  $now->format('jS');
  
    //check current user
    $user = \Auth::user();
    $employeeDetails = Feedback::findOrFail($id);
    $employee = ucwords($employeeDetails->deployment->employee->last_name).", ".ucwords($employeeDetails->deployment->employee->first_name)." ".ucwords($employeeDetails->deployment->employee->middle_name);
    $company = ucwords($employeeDetails->deployment->client->name);

    // return view("pdf.for_regularization");
    // view()->share('for_regularization', $user);
    $pdf = \PDF::loadView('pdf.best_performer',[
       'company'=>  $company,
       'employee'=>$employee,
       'month' => $month,
       'year' => $year,
       'day' =>$day
    ]);

    $pdf->setPaper('a4', 'landscape');


    $log = new Log();
    $log->log = "User " . \Auth::user()->email . " generate PDF For Regularization at " . Carbon::now();
    $log->creator_id =  \Auth::user()->id;
    $log->updater_id =  \Auth::user()->id;
    $log->save();

    return $pdf->stream("Best-Performer-" .  $month."-". $year."-".$employee. ".pdf");
  }

  public function generatePayslip(Request $request,$id)
  {
    set_time_limit(300);
   
    $payslip = Payslip::find($id);
    $payroll = Payroll::find($payslip->payroll_id);
    $startDate = Carbon::parse($payroll->start_date)->format('Y-m-d');
    $endDate =  Carbon::parse($payroll->end_date)->format('Y-m-d');
    $payrollDate = Carbon::parse($payroll->end_date)->addDays(10)->format('Y-m-d');
    //check current user
    $user = \Auth::user();
    $employeeDetails = Deployment::findOrFail($payslip->deployment_id);
    $totalHoursWorked = Attendance::where('deployment_id', $payslip->deployment_id)
        ->where('status', 'Present')
        ->whereNull('deleted_at')
        ->whereBetween('attendance_date', [$startDate, $endDate])
        ->sum('hours_worked');

    $totalHoursWorkedDays =Attendance::where('deployment_id', $payslip->deployment_id)
                        ->where('status', 'Present')
                        ->whereNull('deleted_at')
                        ->whereBetween('attendance_date', [$startDate, $endDate])
                        ->count();
    $totalHoursOverTime = OverTime::where('deployment_id', $payslip->deployment_id)
                        ->whereNull('deleted_at')
                        ->whereBetween('overtime_date', [$startDate, $endDate])
                        ->sum(DB::raw('TIME_TO_SEC(duration) / 60'));
    $totalHoursLate = LateTime::where('deployment_id', $payslip->deployment_id)
                        ->whereNull('deleted_at')
                        ->whereBetween('latetime_date', [$startDate, $endDate])
                        ->sum(DB::raw('TIME_TO_SEC(duration) / 60'));
    $employee = ucwords($employeeDetails->employee->last_name).", ".ucwords($employeeDetails->employee->first_name)." ".ucwords($employeeDetails->employee->middle_name);
    $company = ucwords($employeeDetails->client->name);
    $position = ucwords($employeeDetails->position);
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
        $previousDayAttendance = Attendance::where('deployment_id', $payslip->deployment_id)
            ->where('status', 'Present')
            ->whereNull('deleted_at')
            ->where('attendance_date', $previousDay)
            ->first();

        $holidayAttendance = Attendance::where('deployment_id', $payslip->deployment_id)
            ->where('status', 'Present')
            ->whereNull('deleted_at')
            ->where('attendance_date', $holidayDate)
            ->first();

        $nextDayAttendance = Attendance::where('deployment_id', $payslip->deployment_id)
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
            $totalHoursOverTimeHoliday = OverTime::where('deployment_id', $payslip->deployment_id)
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
    $totalCompensation = floatval($basicSalaryTotal) + $deMinimisBenefits + floatval($overTimeTotal) + (floatval($payslip->other_pay) ?? 0) + floatval($totalHolidayPay);

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
    $totalWorkedDaysFor13thMonth = Attendance::where('deployment_id', $payslip->deployment_id)
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

    $include_13th_month = $payslip->include_thirteen_month_pay ?? false;
    $totalCompensationWith13thMonthPay = $include_13th_month ? $totalCompensation + $thirteenthMonthPay :  $totalCompensation;
    $totalDeduction = ($employeeDetails->salary->sss / 2 ?? 0) +
      ($employeeDetails->salary->philhealth / 2 ?? 0) +
      ($employeeDetails->salary->pagibig / 2 ?? 0) +
      ($employeeDetails->salary->uniform ?? 0) +
      ($tax ?? 0) +
      ($lateTotalDeduction ?? 0) +
      ($request->other_deduction ?? 0) +
      $totalGeneralDeductions +
      $thirteenthMonthTax; // Optional: include 13th month tax in deductions
    $netPay =  $totalCompensationWith13thMonthPay - $totalDeduction;
    $tax = $totalCompensation >= $employeeDetails->salary->tax_salary_range ? $totalCompensation / $employeeDetails->salary->tax ?? 0 : 0;
    // ===== END: 13th Month Pay Computation =====

    $pdf = \PDF::loadView('pdf.payslip',[
      'employee' => $employee,
      'company' => $company,
      'position' => $position,
      'employeeDetails' => $employeeDetails,
      'tax' => $tax,
      'basicSalaryTotal' => $basicSalaryTotal,
      'deMinimisBenefits' => $deMinimisBenefits ?? 0,
      'totalCompensation' => $totalCompensation ?? 0,
      'startDate' =>  $startDate,
      'endDate' =>  $endDate,
      'overTimeTotal' => $overTimeTotal,
      'totalDeduction' => $totalDeduction,
      'netPay' => $netPay  < 0 ? 0 : $netPay,
      'payrollDate' => $payrollDate,
      'basicSalary' => $basicSalary,
      'ratePerHour' => $ratePerHour,
      'totalHoursWorked' =>   $totalHoursWorked,
      'totalHoursLate' => floatval($totalHoursLate / 60),
      'totalHoursOverTime' => floatval($totalHoursOverTime /60),
      'lateTotalDeduction' => $lateTotalDeduction,
      'totalHoursWorkedDays' => $totalHoursWorkedDays,
      'totalHolidayPay' =>  $totalHolidayPay,
      'otherDeduction' => $payslip->other_deduction,
      'otherPay' => $payslip->other_pay,
      'generalDeductions' => $generalDeductions,
      'thirteenthMonthPay' => number_format($thirteenthMonthPay,2),
      'thirteenthMonthTax' => number_format($thirteenthMonthTax, 2)
    ]);


    $log = new Log();
    $log->log = "User " . \Auth::user()->email . " generate PDF For Payslip at " . Carbon::now();
    $log->creator_id =  \Auth::user()->id;
    $log->updater_id =  \Auth::user()->id;
    $log->save();

    return $pdf->stream("Payslip-" .  $startDate ."-".$endDate. ".pdf");
  }
}
