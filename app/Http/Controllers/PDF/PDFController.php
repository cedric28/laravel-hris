<?php

namespace App\Http\Controllers\PDF;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Log;
use App\Feedback;
use App\Deployment;
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
}
