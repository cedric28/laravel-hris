<?php

namespace App\Http\Controllers\PDF;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Log;
use App\Feedback;
use App\Deployment;
use App\Attendance;
use App\OverTime;
use Carbon\Carbon;
use PDF;

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
    // $now = Carbon::now();
    // $year =  $now->year;
    // $month =  $now->translatedFormat('F');
    // $day =  $now->format('jS');
    $start_date = $request->input('start_date');
    $end_date = $request->input('end_date');
    $startDate = Carbon::parse($request->input('start_date'))->format('Y-m-d');
    $endDate =  Carbon::parse($request->input('end_date'))->format('Y-m-d');
    // //check current user
    $user = \Auth::user();
    $employeeDetails = Deployment::findOrFail($id);
    $totalHoursWorked = Attendance::where('deployment_id', $id)
                        ->whereBetween('attendance_date', [$startDate, $endDate])
                        ->sum('hours_worked');
    $totalHoursOverTime = OverTime::where('deployment_id', $id)
                        ->whereBetween('overtime_date', [$startDate, $endDate])
                        ->sum('duration');

    $employee = ucwords($employeeDetails->employee->last_name).", ".ucwords($employeeDetails->employee->first_name)." ".ucwords($employeeDetails->employee->middle_name);
    $company = ucwords($employeeDetails->client->name);
    $position = ucwords($employeeDetails->position);
    $basicSalary =  $employeeDetails->salary->basic_salary ?? 0;
    $basicSalaryTotal =  ($basicSalary / 8 ) * $totalHoursWorked;
    $overTimeTotal = ($totalHoursOverTime / 8 ) * $totalHoursWorked;
    $deMinimisBenefits = ($employeeDetails->salary->meal_allowance ?? 0 ) + ($employeeDetails->salary->laundry_allowance ?? 0 ) + ($employeeDetails->salary->transportation_allowance ?? 0 ) + ($employeeDetails->salary->cola ?? 0 );
    $totalCompensation = $basicSalaryTotal + $deMinimisBenefits + $overTimeTotal;
    $tax = $basicSalaryTotal >= 21000 ? $basicSalaryTotal / $employeeDetails->salary->tax ?? 0 : 0;
    $totalDeduction = ($employeeDetails->salary->sss ?? 0) + ($employeeDetails->salary->philhealth ?? 0) + ($employeeDetails->salary->pagibig ?? 0) + ($employeeDetails->salary->uniform ?? 0) + ($tax ?? 0);
    $netPay =  $totalCompensation - $totalDeduction;
  //  // return view("pdf.payslip");
  //   // // view()->share('for_regularization', $user);
    $pdf = \PDF::loadView('pdf.payslip',[
      'employee' => $employee,
      'company' => $company,
      'position' => $position,
      'employeeDetails' => $employeeDetails,
      'tax' => $tax,
      'basicSalaryTotal' => $basicSalaryTotal,
      'deMinimisBenefits' => $deMinimisBenefits ?? 0,
      'totalCompensation' => $totalCompensation ?? 0,
      'startDate' =>  $start_date,
      'endDate' =>  $end_date,
      'overTimeTotal' => $overTimeTotal,
      'totalDeduction' => $totalDeduction,
      'netPay' => $netPay  < 0 ? 0 : $netPay,
    ]);


    $log = new Log();
    $log->log = "User " . \Auth::user()->email . " generate PDF For Regularization at " . Carbon::now();
    $log->creator_id =  \Auth::user()->id;
    $log->updater_id =  \Auth::user()->id;
    $log->save();

    return $pdf->stream("Payslip-" .  $startDate ."-".$endDate. ".pdf");
  }
}
