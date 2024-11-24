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
		$endOfMonth = Carbon::now()->endOfMonth()->subDays(3);

		//get the total number of data in User table
		$totalData = Deployment::select('deployments.id as id', DB::raw('CONCAT(employees.last_name, ", ", employees.first_name, " ", employees.middle_name) AS full_name'), 'clients.name as company')
		->join('employees', 'deployments.employee_id', '=', 'employees.id')
		->join('clients', 'deployments.client_id', '=', 'clients.id')
		->whereHas('attendances', function ($query) use ($currentMonth, $currentYear, $endOfMonth) {
						$query->whereYear('attendance_date', $currentYear)
												->whereMonth('attendance_date', $currentMonth)
												->where('attendance_date', '>=', $endOfMonth)
												->where('status', 'Present')
												->where('hours_worked', '>=', 8);
			})->whereNotExists(function ($query) use ($currentMonth, $currentYear, $endOfMonth) {
						$query->from('late_times')
												->whereYear('latetime_date', $currentYear)
												->whereMonth('latetime_date', $currentMonth);
			})->whereDoesntHave('attendances', function ($query) use  ($currentMonth, $currentYear, $endOfMonth) {
						$query->whereYear('attendance_date', $currentYear)
												->whereMonth('attendance_date', $currentMonth)
												->where('status', '<>', 'Present');
			})
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
						$posts =  Deployment::select('deployments.id as id', DB::raw('CONCAT(employees.last_name, ", ", employees.first_name, " ", employees.middle_name) AS full_name'), 'clients.name as company')
						->join('employees', 'deployments.employee_id', '=', 'employees.id')
						->join('clients', 'deployments.client_id', '=', 'clients.id')
						->whereHas('attendances', function ($query) use ($currentMonth, $currentYear, $endOfMonth) {
							$query->whereYear('attendance_date', $currentYear)
													->whereMonth('attendance_date', $currentMonth)
													->where('attendance_date', '>=', $endOfMonth)
													->where('status', 'Present')
													->where('hours_worked', '>=', 8);
				})->whereNotExists(function ($query) use ($currentMonth, $currentYear, $endOfMonth) {
							$query->from('late_times')
													->whereYear('latetime_date', $currentYear)
													->whereMonth('latetime_date', $currentMonth);
				})->whereDoesntHave('attendances', function ($query) use ($currentMonth, $currentYear,$endOfMonth) {
							$query->whereYear('attendance_date', $currentYear)
													->whereMonth('attendance_date', $currentMonth)
													->where('status', '<>', 'Present');
				})
			->offset($start)
			->limit($limit)
			->orderBy($order, $dir)
			->get();

			//total number of filtered data
			$totalFiltered =Deployment::select('deployments.id as id', DB::raw('CONCAT(employees.last_name, ", ", employees.first_name, " ", employees.middle_name) AS full_name'), 'clients.name as company')
												->join('employees', 'deployments.employee_id', '=', 'employees.id')
												->join('clients', 'deployments.client_id', '=', 'clients.id')
												->whereHas('attendances', function ($query) use ($currentMonth, $currentYear, $endOfMonth) {
													$query->whereYear('attendance_date', $currentYear)
																			->whereMonth('attendance_date', $currentMonth)
																			->where('attendance_date', '>=', $endOfMonth)
																			->where('status', 'Present')
																			->where('hours_worked', '>=', 8);
										})->whereNotExists(function ($query) use ($currentMonth, $currentYear, $endOfMonth) {
													$query->from('late_times')
																			->whereYear('latetime_date', $currentYear)
																			->whereMonth('latetime_date', $currentMonth);
																		})->whereDoesntHave('attendances', function ($query) use ($currentMonth, $currentYear,$endOfMonth) {
																			$query->whereYear('attendance_date', $currentYear)
																									->whereMonth('attendance_date', $currentMonth)
																									->where('status', '<>', 'Present');
											})->count();
		} else {
			$search = $request->input('search.value');

			$posts =	Attendance::select('deployments.id as id', DB::raw('CONCAT(employees.last_name, ", ", employees.first_name, " ", employees.middle_name) AS full_name'), 'clients.name as company')
						->join('employees', 'deployments.employee_id', '=', 'employees.id')
						->join('clients', 'deployments.client_id', '=', 'clients.id')
						->whereHas('attendances', function ($query) use ($currentMonth, $currentYear, $endOfMonth) {
							$query->whereYear('attendance_date', $currentYear)
													->whereMonth('attendance_date', $currentMonth)
													->where('attendance_date', '>=', $endOfMonth)
													->where('status', 'Present')
													->where('hours_worked', '>=', 8);
				})->whereNotExists(function ($query) use ($currentMonth, $currentYear, $endOfMonth) {
							$query->from('late_times')
													->whereYear('latetime_date', $currentYear)
													->whereMonth('latetime_date', $currentMonth);
												})->whereDoesntHave('attendances', function ($query) use ($currentMonth, $currentYear,$endOfMonth) {
													$query->whereYear('attendance_date', $currentYear)
																			->whereMonth('attendance_date', $currentMonth)
																			->where('status', '<>', 'Present');
					})
				->orWhereHas('employee', function ($query) use ($search) {
					$query->where('first_name', 'like', "%{$search}%")
									->orWhere('middle_name', 'like', "%{$search}%")
									->orWhere('last_name', 'like', "%{$search}%");
			})
			->orWhereHas('client', function ($query) use ($search) {
					$query->where('name', 'like', "%{$search}%");
			})
			->offset($start)
			->limit($limit)
			->orderBy($order, $dir)
			->get();

			//total number of filtered data matching the search value request in the Supplier table	
			$totalFiltered = Deployment::select('deployments.id as id', DB::raw('CONCAT(employees.last_name, ", ", employees.first_name, " ", employees.middle_name) AS full_name'), 'clients.name as company')
			->join('employees', 'deployments.employee_id', '=', 'employees.id')
			->join('clients', 'deployments.client_id', '=', 'clients.id')
			->whereHas('attendances', function ($query) use ($currentMonth, $currentYear, $endOfMonth) {
				$query->whereYear('attendance_date', $currentYear)
										->whereMonth('attendance_date', $currentMonth)
										->where('attendance_date', '>=', $endOfMonth)
										->where('status', 'Present')
										->where('hours_worked', '>=', 8);
	})->whereNotExists(function ($query) use ($currentMonth, $currentYear, $endOfMonth) {
				$query->from('late_times')
										->whereYear('latetime_date', $currentYear)
										->whereMonth('latetime_date', $currentMonth);
									})->whereDoesntHave('attendances', function ($query) use ($currentMonth, $currentYear,$endOfMonth) {
										$query->whereYear('attendance_date', $currentYear)
																->whereMonth('attendance_date', $currentMonth)
																->where('status', '<>', 'Present');
		})
			->orWhereHas('employee', function ($query) use ($search) {
							$query->where('first_name', 'like', "%{$search}%")
											->orWhere('middle_name', 'like', "%{$search}%")
											->orWhere('last_name', 'like', "%{$search}%");
			})
			->orWhereHas('client', function ($query) use ($search) {
							$query->where('name', 'like', "%{$search}%");
			})->count();
		}

		$data = array();

		if ($posts) {
			//loop posts collection to transfer in another array $nestedData
			foreach ($posts as $r) {
				$nestedData['fullname'] = $r->full_name;
				$nestedData['company'] = $r->company;
				$nestedData['action'] = '
						<button name="generate_pdf" title="generate pdf" id="generate_pdf" data-id="' . $r->id . '" class="btn bg-gradient-info btn-sm">Generate Certificate</button>
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
