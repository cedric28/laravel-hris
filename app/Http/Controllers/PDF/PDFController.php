<?php

namespace App\Http\Controllers\PDF;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Log;
use Carbon\Carbon;
use PDF;

class PDFController extends Controller
{
  public function generatePerfectAttendance(Request $request)
    {
     
      
        $now = Carbon::now();
        $yearNow =  $now->year;
        $currentMonth = Carbon::now()->month;
      
        //check current user
        $user = \Auth::user();
        $fullName = $user->last_name . ", " . $user->first_name;
        // view()->share('sales', $sales);
        $pdf = \PDF::loadView('pdf.perfect_attendance', [
         
        ]);

        $pdf->setPaper('A4', 'landscape');

        $log = new Log();
        $log->log = "User " . \Auth::user()->email . " generate Perfect Attendance at " . Carbon::now();
        $log->creator_id =  \Auth::user()->id;
        $log->updater_id =  \Auth::user()->id;
        $log->save();

        return $pdf->download("Perfect-Attendance-" .  $currentMonth . ".pdf");
    }
}
