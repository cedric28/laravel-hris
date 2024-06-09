<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Deployment;
use App\Employee;
use App\Client;
use App\Attendance;
use Carbon\Carbon;
use DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $this->authorize("isAdmin");
        $now = Carbon::now();
        $yearNow =  $now->year;

        $user = User::count();
        $totalEmployees = Deployment::where('status','new')->whereYear('start_date', $yearNow)->count();
        $totalApplicants = Employee::whereDoesntHave('deployments', function ($query) {
            $query->where('status', 'new');
            })->count();
        $totalClients = Client::count();

        $yearlyRegular = DB::table('deployments')
            ->select(DB::raw('YEAR(start_date) as year'), DB::raw('COUNT(*) as total_regular_employees'))
            ->where('status', 'regular')
            ->groupBy(DB::raw('YEAR(start_date)'))
            ->orderBy(DB::raw('YEAR(start_date)'), 'asc')
            ->get();
        
        $currentYear = Carbon::now()->year;
		$threeDaysBeforeEndOfMonth = 3;
        $perfectAttendanceMonths = Attendance::select(DB::raw('MONTHNAME(attendance_date) as month'), DB::raw('COALESCE(COUNT(DISTINCT deployments.id),0) as total'))
            ->join('deployments', 'deployments.id', '=', 'attendances.deployment_id')
            ->whereYear('attendance_date', $currentYear)
            ->whereNotIn(DB::raw('DAYOFWEEK(attendance_date)'), [1, 7]) // Excluding weekends
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('late_times')
                      ->whereRaw('late_times.latetime_date = attendances.attendance_date');
            })
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('deployments')
                      ->whereRaw('deployments.id = attendances.deployment_id')
                      ->where('deployments.status', 'new');
            })
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('attendances')
                    ->whereRaw('attendances.deployment_id = deployments.id')
                    ->where('attendances.status', '!=', 'Absent');
            })
            ->whereRaw("DAY(LAST_DAY(attendance_date)) - DAY(attendance_date) <= ?", [$threeDaysBeforeEndOfMonth])
            ->groupBy(DB::raw('MONTHNAME(attendance_date)'))
            ->get();

        
        // Output the result
        $allMonths = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthName = date('F', mktime(0, 0, 0, $i, 1));
            $allMonths[] = [
                'month' => $monthName,
                'total' => 0,
            ];
        }

        $perfectAttendanceMonths->each(function ($month) use (&$allMonths) {
            $index = array_search($month->month, array_column($allMonths, 'month'));
            if ($index !== false) {
                $allMonths[$index]['total'] = $month->total;
            }
        });
        
        
        return view('home',[
            "user" => $user,
            'year' => $yearNow,
            'yearlyRegular' => $yearlyRegular,
            'perfectAttendanceMonths' => $allMonths,
            'totalEmployees' => $totalEmployees,
            'totalApplicants' => $totalApplicants,
            'totalClients' => $totalClients
        ]);
    }
}
