<?php

namespace App\Http\Controllers\PerfectAttendance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Deployment;
use App\Attendance;
use Validator, Hash, DB;
use Carbon\Carbon;

class PerfectAttendanceFetchController extends Controller
{
 public function fetchPerfectAttendance(Request $request)
	{
		

		//column list in the table Prpducts
		$columns = array(
			0 => 'first_name',
			1 => 'company',
			2 => 'action'
		);

		$currentMonth = Carbon::now()->month;
		$currentYear = Carbon::now()->year;
		$threeDaysBeforeEndOfMonth = Carbon::now()->endOfMonth()->subDays(3)->day;

		//get the total number of data in User table
		$totalData = Attendance::select('deployments.id as id', DB::raw('CONCAT(employees.last_name, ", ", employees.first_name, " ", employees.middle_name) AS full_name'), 'clients.name as company')
		->join('deployments', 'deployments.id', '=', 'attendances.deployment_id')
		->join('employees', 'deployments.employee_id', '=', 'employees.id')
		->join('clients', 'deployments.client_id', '=', 'clients.id')
		->where(function ($query) use ($currentMonth,$threeDaysBeforeEndOfMonth,	$currentYear) {
			 $query->whereYear('attendance_date', $currentYear)
           ->whereMonth('attendance_date', $currentMonth)
										->whereNotIn(DB::raw('DAYOFWEEK(attendance_date)'), [1, 7])
										->where('attendances.status', 'Present')
										->whereDoesntHave('lates', function ($query) use ($currentMonth, $threeDaysBeforeEndOfMonth,	$currentYear) {
													 $query->whereYear('attendance_date', $currentYear)
            						->whereMonth('attendance_date', $currentMonth)
																		->whereRaw("DAY(LAST_DAY(created_at)) - DAY(created_at) <=?", [$threeDaysBeforeEndOfMonth]);
										});
		})
    ->groupBy(
    'deployments.id',
    'employees.first_name',
    'employees.middle_name',
    'employees.last_name',
    'clients.name',
    'attendances.attendance_date' // Add this line
)
				->havingRaw("SUM(CASE WHEN attendances.status = 'Present' AND DAY(LAST_DAY(attendances.attendance_date)) - DAY(attendances.attendance_date) <= 3 THEN 1 ELSE 0 END) = DAY(LAST_DAY(attendance_date)) - DAY(MIN(attendance_date)) + 1")
				->havingRaw("SUM(hours_worked) >= 8 * (DAY(LAST_DAY(attendances.attendance_date)) - DAY(MIN(attendances.attendance_date)) + 1)")
->count();
		//total number of data that will show in the datatable default 10
		$limit = $request->input('length');
		//start number for pagination ,default 0
		$start = $request->input('start');
		//order list of the column
		$order = $columns[$request->input('order.0.column')];
		//order by ,default asc 
		$dir = $request->input('order.0.dir');

		//check if user search for a value in the User datatable
		if (empty($request->input('search.value'))) {
			$posts =  Attendance::select('deployments.id as id', DB::raw('CONCAT(employees.last_name, ", ", employees.first_name, " ", employees.middle_name) AS full_name'), 'clients.name as company', 'attendances.attendance_date' )
			->join('deployments', 'deployments.id', '=', 'attendances.deployment_id')
			->join('employees', 'deployments.employee_id', '=', 'employees.id')
			->join('clients', 'deployments.client_id', '=', 'clients.id')
			->where(function ($query) use ($currentMonth, $threeDaysBeforeEndOfMonth, $currentYear) {
				 $query->whereYear('attendance_date', $currentYear)
            ->whereMonth('attendance_date', $currentMonth)
											->whereNotIn(DB::raw('DAYOFWEEK(attendance_date)'), [1, 7])
											->where('attendances.status', 'Present')
											->whereDoesntHave('lates', function ($query) use ($currentMonth, $threeDaysBeforeEndOfMonth, $currentYear) {
														 $query->whereYear('attendance_date', $currentYear)
            							->whereMonth('attendance_date', $currentMonth)
																			->whereRaw("DAY(LAST_DAY(created_at)) - DAY(created_at) <=?", [$threeDaysBeforeEndOfMonth]);
											});
			})
	    ->groupBy('deployments.id', 'employees.first_name', 'employees.middle_name', 'employees.last_name', 'clients.name','attendances.attendance_date' )
			->havingRaw("SUM(CASE WHEN attendances.status = 'Present' AND DAY(LAST_DAY(attendances.attendance_date)) - DAY(attendances.attendance_date) <= 3 THEN 1 ELSE 0 END) = DAY(LAST_DAY(attendance_date)) - DAY(MIN(attendance_date)) + 1")
			->havingRaw("SUM(hours_worked) >= 8 * (DAY(LAST_DAY(attendances.attendance_date)) - DAY(MIN(attendances.attendance_date)) + 1)")
			->offset($start)
			->limit($limit)
			->orderBy($order, $dir)
			->get();

			//total number of filtered data
			$totalFiltered =Attendance::select('deployments.id as id', DB::raw('CONCAT(employees.last_name, ", ", employees.first_name, " ", employees.middle_name) AS full_name'), 'clients.name as company', 'attendances.attendance_date' )
			->join('deployments', 'deployments.id', '=', 'attendances.deployment_id')
			->join('employees', 'deployments.employee_id', '=', 'employees.id')
			->join('clients', 'deployments.client_id', '=', 'clients.id')
			->where(function ($query) use ($currentMonth, $threeDaysBeforeEndOfMonth, $currentYear) {
				 $query->whereYear('attendance_date', $currentYear)
            ->whereMonth('attendance_date', $currentMonth)
											->whereNotIn(DB::raw('DAYOFWEEK(attendance_date)'), [1, 7])
											->where('attendances.status', 'Present')
											->whereDoesntHave('lates', function ($query) use ($currentMonth, $threeDaysBeforeEndOfMonth, $currentYear) {
														 $query->whereYear('attendance_date', $currentYear)
            							->whereMonth('attendance_date', $currentMonth)
																			->whereRaw("DAY(LAST_DAY(created_at)) - DAY(created_at) <=?", [$threeDaysBeforeEndOfMonth]);
											});
			})
	    ->groupBy(
    'deployments.id',
    'employees.first_name',
    'employees.middle_name',
    'employees.last_name',
    'clients.name',
    'attendances.attendance_date' // Add this line
)
			->havingRaw("SUM(CASE WHEN attendances.status = 'Present' AND DAY(LAST_DAY(attendances.attendance_date)) - DAY(attendances.attendance_date) <= 3 THEN 1 ELSE 0 END) = DAY(LAST_DAY(attendance_date)) - DAY(MIN(attendance_date)) + 1")
			->havingRaw("SUM(hours_worked) >= 8 * (DAY(LAST_DAY(attendances.attendance_date)) - DAY(MIN(attendances.attendance_date)) + 1)")
			->count();
		} else {
			$search = $request->input('search.value');

			$posts =	 Attendance::select('deployments.id as id', DB::raw('CONCAT(employees.last_name, ", ", employees.first_name, " ", employees.middle_name) AS full_name'), 'clients.name as company', 'attendances.attendance_date' )
			->join('deployments', 'deployments.id', '=', 'attendances.deployment_id')
			->join('employees', 'deployments.employee_id', '=', 'employees.id')
			->join('clients', 'deployments.client_id', '=', 'clients.id')
			->where(function ($query) use ($currentMonth, $threeDaysBeforeEndOfMonth, $currentYear) {
				 $query->whereYear('attendance_date', $currentYear)
            ->whereMonth('attendance_date', $currentMonth)
											->whereNotIn(DB::raw('DAYOFWEEK(attendance_date)'), [1, 7])
											->where('attendances.status', 'Present')
											->whereDoesntHave('lates', function ($query) use ($currentMonth, $threeDaysBeforeEndOfMonth, $currentYear) {
														 $query->whereYear('attendance_date', $currentYear)
            							->whereMonth('attendance_date', $currentMonth)
																			->whereRaw("DAY(LAST_DAY(created_at)) - DAY(created_at) <=?", [$threeDaysBeforeEndOfMonth]);
											});
			})
	    ->groupBy(
    'deployments.id',
    'employees.first_name',
    'employees.middle_name',
    'employees.last_name',
    'clients.name',
    'attendances.attendance_date' // Add this line
)
			->havingRaw("SUM(CASE WHEN attendances.status = 'Present' AND DAY(LAST_DAY(attendances.attendance_date)) - DAY(attendances.attendance_date) <= 3 THEN 1 ELSE 0 END) = DAY(LAST_DAY(attendance_date)) - DAY(MIN(attendance_date)) + 1")
			->havingRaw("SUM(hours_worked) >= 8 * (DAY(LAST_DAY(attendances.attendance_date)) - DAY(MIN(attendances.attendance_date)) + 1)")
			->whereHas('deployments.employee', function ($query) use ($search) {
							$query->where('first_name', 'like', "%{$search}%")
											->orWhere('middle_name', 'like', "%{$search}%")
											->orWhere('last_name', 'like', "%{$search}%");
			})
			->orWhereHas('deployments.client', function ($query) use ($search) {
							$query->where('name', 'like', "%{$search}%");
			})
			->offset($start)
			->limit($limit)
			->orderBy($order, $dir)
			->get();

			//total number of filtered data matching the search value request in the Supplier table	
			$totalFiltered = Attendance::select('deployments.id as id', DB::raw('CONCAT(employees.last_name, ", ", employees.first_name, " ", employees.middle_name) AS full_name'), 'clients.name as company')
			->join('deployments', 'deployments.id', '=', 'attendances.deployment_id')
			->join('employees', 'deployments.employee_id', '=', 'employees.id')
			->join('clients', 'deployments.client_id', '=', 'clients.id')
			->where(function ($query) use ($currentMonth, $threeDaysBeforeEndOfMonth, $currentYear) {
				 $query->whereYear('attendance_date', $currentYear)
            ->whereMonth('attendance_date', $currentMonth)
											->whereNotIn(DB::raw('DAYOFWEEK(attendance_date)'), [1, 7])
											->where('attendances.status', 'Present')
											->whereDoesntHave('lates', function ($query) use ($currentMonth, $threeDaysBeforeEndOfMonth, $currentYear) {
														 $query->whereYear('attendance_date', $currentYear)
            							->whereMonth('attendance_date', $currentMonth)
																			->whereRaw("DAY(LAST_DAY(created_at)) - DAY(created_at) <=?", [$threeDaysBeforeEndOfMonth]);
											});
			})
	    ->groupBy(
    'deployments.id',
    'employees.first_name',
    'employees.middle_name',
    'employees.last_name',
    'clients.name',
    'attendances.attendance_date' // Add this line
)
			->havingRaw("SUM(CASE WHEN attendances.status = 'Present' AND DAY(LAST_DAY(attendances.attendance_date)) - DAY(attendances.attendance_date) <= 3 THEN 1 ELSE 0 END) = DAY(LAST_DAY(attendance_date)) - DAY(MIN(attendance_date)) + 1")
			->havingRaw("SUM(hours_worked) >= 8 * (DAY(LAST_DAY(attendances.attendance_date)) - DAY(MIN(attendances.attendance_date)) + 1)")
			->whereHas('deployments.employee', function ($query) use ($search) {
							$query->where('first_name', 'like', "%{$search}%")
											->orWhere('middle_name', 'like', "%{$search}%")
											->orWhere('last_name', 'like', "%{$search}%");
			})
			->orWhereHas('deployments.client', function ($query) use ($search) {
							$query->where('name', 'like', "%{$search}%");
			})
->count();
		}

		$data = array();

		if ($posts) {
			//loop posts collection to transfer in another array $nestedData
			foreach ($posts as $r) {
				$nestedData['fullname'] = $r->full_name;
				$nestedData['company'] = $r->company;
				$nestedData['action'] = '
						<button name="generate_pdf" id="generate_pdf" data-id="' . $r->id . '" class="btn bg-gradient-info btn-sm">Generate Certificate</button>
					';
				$data[] = $nestedData;
			}
		}

		$json_data = array(
			"draw"			    => intval($request->input('draw')),
			"recordsTotal"	    => intval($totalData),
			"recordsFiltered"   => intval($totalFiltered),
			"data"			    => $data
		);

		//return the data in json response
		return response()->json($json_data);
	}
}
