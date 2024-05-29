<?php

namespace App\Http\Controllers\PerfectAttendance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Deployment;
use Validator, Hash, DB;
use Carbon\Carbon;

class PerfectAttendanceFetchController extends Controller
{
 public function fetchPerfectAttendance(Request $request)
	{
		$current_month = now()->startOfMonth()->format('Y-m');
		$currentMonth = Carbon::now()->month;
		//column list in the table Prpducts
		$columns = array(
			0 => 'first_name',
			1 => 'company',
			2 => 'action'
		);

		$endOfMonth = Carbon::now()->endOfMonth();
		$threeDaysBeforeEndOfMonth = $endOfMonth->subDays(3);

		//get the total number of data in User table
		$totalData = Deployment::whereHas('attendances', function ($query) use ($current_month, $threeDaysBeforeEndOfMonth) {
			$query->whereBetween('attendance_date', [$current_month . '-01', $current_month . '-31'])
							->whereNotIn('day_of_week', [6, 0])
							->whereDate('attendance_date', '<=', $threeDaysBeforeEndOfMonth);
})
	->whereDoesntHave('lates', function ($query) use ($currentMonth) {
		$query->whereMonth('created_at', $currentMonth);
	})
->where([
			['deployments.deleted_at', '=', null]
])->count();
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
			//get all the User data
// 			whereHas('attendances', function ($query) use ($currentMonth) {
//     $query->where(function ($query) use ($currentMonth) {
//         // Attendance within the current month, excluding weekends
//         $query->whereBetween('attendance_date', [$currentMonth . '-01', $currentMonth . '-31'])
//               ->whereNotIn('day_of_week', [6, 0]);
        
//         // Additional check for perfect attendance
//         $query->orWhere(function ($query) use ($currentMonth) {
//             // Get the last three days of the current month
//             $lastThreeDays = Carbon::now()->endOfMonth()->subDays(2)->toDateString();
            
//             // Check if there are attendances for the last three days
//             $query->where('attendance_date', '>=', $lastThreeDays)
//                   ->where('attendance_date', '<=', Carbon::now()->endOfMonth()->toDateString())
//                   ->whereNotIn('day_of_week', [6, 0]);
//         });
//     });
// })
			$posts = Deployment::select('deployments.id as id',DB::raw('CONCAT(employees.last_name, ", ", employees.first_name, " ", employees.middle_name) AS full_name'), 'clients.name as company')
																->join('employees', 'deployments.employee_id', '=', 'employees.id')
																->join('clients', 'deployments.client_id', '=', 'clients.id')
																->whereHas('attendances', function ($query) use ($current_month, $threeDaysBeforeEndOfMonth) {
																				$query->whereBetween('attendance_date', [$current_month . '-01', $current_month . '-31'])
																								->whereNotIn('day_of_week', [6, 0])
																								->whereDate('attendance_date', '<=', $threeDaysBeforeEndOfMonth);
																})
																->whereDoesntHave('lates', function ($query) use ($currentMonth) {
																	$query->whereMonth('created_at', $currentMonth);
																})
                ->where([
                    ['deployments.deleted_at', '=', null]
                ])
															->offset($start)
															->limit($limit)
															->orderBy($order, $dir)
															->get();

			//total number of filtered data
			$totalFiltered = Deployment::whereHas('attendances', function ($query) use ($current_month, $threeDaysBeforeEndOfMonth) {
										$query->whereBetween('attendance_date', [$current_month . '-01', $current_month . '-31'])
														->whereNotIn('day_of_week', [6, 0])
														->whereDate('attendance_date', '<=', $threeDaysBeforeEndOfMonth);
						})
						->whereDoesntHave('lates', function ($query) use ($currentMonth) {
							$query->whereMonth('created_at', $currentMonth);
						})
						->where([
										['deployments.deleted_at', '=', null]
						])->count();
		} else {
			$search = $request->input('search.value');

			$posts =	Deployment::select('deployments.id as id',DB::raw('CONCAT(employees.last_name, ", ", employees.first_name, " ", employees.middle_name) AS full_name'), 'clients.name as company')
												->join('employees', 'deployments.employee_id', '=', 'employees.id')
												->join('clients', 'deployments.client_id', '=', 'clients.id')
												->whereHas('employee', function ($query) use ($search) {
															$query->where('first_name', 'like', "%{$search}%")
															->orWhere('middle_name', 'like', "%{$search}%")
															->orWhere('last_name', 'like', "%{$search}%");
													})
													->orWhereHas('client', function ($query) use ($search) {
															$query->where('name', 'like', "%{$search}%");
													})
													->whereHas('attendances', function ($query) use ($current_month, $threeDaysBeforeEndOfMonth) {
																	$query->whereBetween('attendance_date', [$current_month . '-01', $current_month . '-31'])
																					->whereNotIn('day_of_week', [6, 0])
																					->whereDate('attendance_date', '<=', $threeDaysBeforeEndOfMonth);
													})
													->whereDoesntHave('lates', function ($query) use ($currentMonth) {
														$query->whereMonth('created_at', $currentMonth);
													})
													->where([
														['deployments.deleted_at', '=', null]
            ])
            ->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

			//total number of filtered data matching the search value request in the Supplier table	
			$totalFiltered = Deployment::select('deployments.id as id',DB::raw('CONCAT(employees.last_name, ", ", employees.first_name, " ", employees.middle_name) AS full_name'), 'clients.name as company')
																				->join('employees', 'deployments.employee_id', '=', 'employees.id')
																				->join('clients', 'deployments.client_id', '=', 'clients.id')
																				->whereHas('employee', function ($query) use ($search) {
																					$query->where('first_name', 'like', "%{$search}%")
																					->orWhere('middle_name', 'like', "%{$search}%")
																					->orWhere('last_name', 'like', "%{$search}%");
																				})
																				->orWhereHas('client', function ($query) use ($search) {
																						$query->where('name', 'like', "%{$search}%");
																				})
																				->whereHas('attendances', function ($query) use ($current_month, $threeDaysBeforeEndOfMonth) {
																							$query->whereBetween('attendance_date', [$current_month . '-01', $current_month . '-31'])
																											->whereNotIn('day_of_week', [6, 0])
																											->whereDate('attendance_date', '<=', $threeDaysBeforeEndOfMonth);
																			})
																				->whereDoesntHave('lates', function ($query) use ($currentMonth) {
																					$query->whereMonth('created_at', $currentMonth);
																				})
																				->where([
																					['deployments.deleted_at', '=', null]
																			])
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
