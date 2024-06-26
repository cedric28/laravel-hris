<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Attendance;
use App\Deployment;
use Carbon\Carbon;
use DB;

class AttendanceFetchController extends Controller
{
    public function fetchAttendance(Request $request)
	{
		//column list in the table Prpducts
		$columns = array(
			0 => 'attendance_date',
			1 => 'attendance_time',
			2 => 'attendance_out',
			4 => 'status',
			5 => 'action'
		);

        $deployment_id = $request->deployment_id;

		//get the total number of data in User table
		$totalData = Attendance::where([
            ['attendances.deployment_id', $deployment_id],
            ['attendances.deleted_at', '=', null]
        ])->count();
		//total number of data that will show in the datatable default 10
		$limit = $request->input('length');
		//start number for pagination ,default 0
		$start = $request->input('start');
		//order list of the column
		$order = $columns[$request->input('order.0.column')];
		//order by ,default asc 
		$dir = $request->input('order.0.dir');
		$dir = ($dir == 'asc') ? 'desc' : $dir;
		//check if user search for a value in the User datatable
		if (empty($request->input('search.value'))) {
			//get all the User data
			$posts = Attendance::select('attendances.attendance_date','attendances.status','attendances.attendance_time','attendances.attendance_out','attendances.id as id')
                ->where([
                    ['attendances.deployment_id', $deployment_id],
                    ['attendances.deleted_at', '=', null]
                ])
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();


			//total number of filtered data
			$totalFiltered = Attendance::where([
                ['attendances.deployment_id', $deployment_id],
                ['attendances.deleted_at', '=', null]
            ])->count();
		} else {
			$search = $request->input('search.value');

			$posts = Attendance::where(function ($query) use ($search) {
                $query->orWhere('attendance_date', 'like', "%{$search}%")
					->orWhere('attendance_time', 'like', "%{$search}%")
					->orWhere('attendance_out', 'like', "%{$search}%")
					->orWhere('status', 'like', "%{$search}%");
			})
            ->where([
                ['attendances.deployment_id', $deployment_id],
                ['attendances.deleted_at', '=', null]
            ])
            ->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

			//total number of filtered data matching the search value request in the Supplier table	
			$totalFiltered = Attendance::where(function ($query) use ($search) {
                $query->orWhere('attendance_date', 'like', "%{$search}%")
																->orWhere('attendance_time', 'like', "%{$search}%")
																->orWhere('attendance_out', 'like', "%{$search}%")
																->orWhere('status', 'like', "%{$search}%");
												})
            ->where([
                ['attendances.deployment_id', $deployment_id],
                ['attendances.deleted_at', '=', null]
            ])->count();
		}


		$data = array();

		if ($posts) {
			//loop posts collection to transfer in another array $nestedData
			foreach ($posts as $r) {
				$nestedData['attendance_date'] = date('M d, Y', strtotime($r->attendance_date));
				$nestedData['attendance_time'] = Carbon::parse( $r->attendance_time)->format('g:i A');
				$nestedData['attendance_out'] =  Carbon::parse($r->attendance_out)->format('g:i A');
				$nestedData['status'] =  $r->status;
				$nestedData['action'] = '
						<button name="delete" id="delete_attendance" data-id="' . $r->id . '" class="btn bg-gradient-danger btn-sm"><i class="fas fa-file-archive"></i></button>
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

	public function fetchAttendanceBulkUpload(Request $request)
	{
		$currentDate = Carbon::today();
		//column list in the table Prpducts
		$columns = array(
			0 => 'company',
			1 => 'full_name',
			2 => 'attendance_date',
			3 => 'status',
			4 => 'action'
		);
		$clientId = $request->client_id;
		//get the total number of data in User table
		$totalData = Deployment::whereHas('attendances', function ($query) use ($currentDate) {
																$query->whereDate('updated_at', $currentDate);
															})
															->where([
																		['deployments.client_id', '=', $clientId],
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
			$posts = Deployment::select('deployments.id as id',DB::raw('CONCAT(employees.last_name, ", ", employees.first_name, " ", employees.middle_name) AS full_name'), 'clients.name as company','attendances.attendance_date','attendances.status')
																->join('employees', 'deployments.employee_id', '=', 'employees.id')
																->join('clients', 'deployments.client_id', '=', 'clients.id')
																->join('attendances', 'deployments.id', '=', 'attendances.deployment_id')
																->whereHas('attendances', function ($query) use ($currentDate) {
																	$query->whereDate('updated_at', $currentDate);
																})
															->where([
																		['deployments.client_id', '=', $clientId],
																		['deployments.deleted_at', '=', null]
															])
															->offset($start)
															->limit($limit)
															->orderBy($order, $dir)
															->get();

			//total number of filtered data
			$totalFiltered = Deployment::select('deployments.id as id',DB::raw('CONCAT(employees.last_name, ", ", employees.first_name, " ", employees.middle_name) AS full_name'), 'clients.name as company','attendances.attendance_date','attendances.status')
																				->join('employees', 'deployments.employee_id', '=', 'employees.id')
																				->join('clients', 'deployments.client_id', '=', 'clients.id')
																				->join('attendances', 'deployments.id', '=', 'attendances.deployment_id')
																				->whereHas('attendances', function ($query) use ($currentDate) {
																					$query->whereDate('updated_at', $currentDate);
																				})
																			->where([
																						['deployments.client_id', '=', $clientId],
																						['deployments.deleted_at', '=', null]
																			])->count();
		} else {
			$search = $request->input('search.value');

			$posts =Deployment::select('deployments.id as id',DB::raw('CONCAT(employees.last_name, ", ", employees.first_name, " ", employees.middle_name) AS full_name'), 'clients.name as company','attendances.attendance_date','attendances.status')
												->join('employees', 'deployments.employee_id', '=', 'employees.id')
												->join('clients', 'deployments.client_id', '=', 'clients.id')
												->join('attendances', 'deployments.id', '=', 'attendances.deployment_id')
												->whereHas('employee', function ($query) use ($search) {
															$query->where('first_name', 'like', "%{$search}%")
															->orWhere('middle_name', 'like', "%{$search}%")
															->orWhere('last_name', 'like', "%{$search}%");
													})
													->orWhereHas('client', function ($query) use ($search) {
															$query->where('name', 'like', "%{$search}%");
													})
													->whereHas('attendances', function ($query) use ($currentDate) {
														$query->whereDate('updated_at', $currentDate);
													})
												->where([
															['deployments.client_id', '=', $clientId],
															['deployments.deleted_at', '=', null]
												])
            ->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

			//total number of filtered data matching the search value request in the Supplier table	
			$totalFiltered = Deployment::select('deployments.id as id',DB::raw('CONCAT(employees.last_name, ", ", employees.first_name, " ", employees.middle_name) AS full_name'), 'clients.name as company','attendances.attendance_date','attendances.status')
																					->join('employees', 'deployments.employee_id', '=', 'employees.id')
																					->join('clients', 'deployments.client_id', '=', 'clients.id')
																					->join('attendances', 'deployments.id', '=', 'attendances.deployment_id')
																					->whereHas('employee', function ($query) use ($search) {
																								$query->where('first_name', 'like', "%{$search}%")
																								->orWhere('middle_name', 'like', "%{$search}%")
																								->orWhere('last_name', 'like', "%{$search}%");
																						})
																						->orWhereHas('client', function ($query) use ($search) {
																								$query->where('name', 'like', "%{$search}%");
																						})
																					->whereHas('attendances', function ($query) use ($currentDate) {
																						$query->whereDate('updated_at', $currentDate);
																					})
																					->where([
																								['deployments.client_id', '=', $clientId],
																								['deployments.deleted_at', '=', null]
																					])
																			->count();
		}

		$data = array();

		if ($posts) {
			//loop posts collection to transfer in another array $nestedData
			foreach ($posts as $r) {
				$nestedData['company'] = $r->company;
				$nestedData['fullname'] = $r->full_name;
				$nestedData['attendance_date'] = date('M d, Y', strtotime($r->attendance_date));
				$nestedData['status'] =  $r->status;
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
