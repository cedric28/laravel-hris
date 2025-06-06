<?php

namespace App\Http\Controllers\Regularization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Deployment;
use Validator, Hash, DB;
use Carbon\Carbon;

class RegularizationFetchController extends Controller
{
 public function fetchForRegularization(Request $request)
	{
        $currentYear = Carbon::now()->year;
		//column list in the table Prpducts
		$columns = array(
			0 => 'first_name',
			1 => 'client_name',
			2 => 'rate',
			3 => 'action'
		);

		//get the total number of data in User table
		$totalData =  Deployment::select('feedback.id as id',
		DB::raw('CONCAT(employees.last_name, ", ", employees.first_name, " ", employees.middle_name) AS full_name'), 'clients.name as company','feedback.rate')
		->join('feedback', 'deployments.id', '=', 'feedback.deployment_id')
		->join('employees', 'deployments.employee_id', '=', 'employees.id')
		->join('clients', 'deployments.client_id', '=', 'clients.id')
		->whereHas('feedbacks', function ($query) use ($currentYear){
				$query->where('rate', '>=', 7)
				->whereYear('created_at', $currentYear);
		})
	 ->whereRaw('(
			SELECT COUNT(DISTINCT MONTH(attendance_date))
			FROM attendances
			WHERE deployment_id = deployments.id
							AND DAYOFWEEK(attendance_date) NOT IN (1, 7) /* Exclude Sundays (1) and Saturdays (7) */
							AND status != "Absent" /* Exclude Absent status */
) >= 11')->whereRaw('(
	SELECT COUNT(*)
	FROM attendances
	WHERE deployment_id = deployments.id
	AND status = "Absent"
) < 10')->where('status','new')->count();
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
            $posts = Deployment::select('feedback.id as id',
												DB::raw('CONCAT(employees.last_name, ", ", employees.first_name, " ", employees.middle_name) AS full_name'), 'clients.name as company','feedback.rate')
												->join('feedback', 'deployments.id', '=', 'feedback.deployment_id')
												->join('employees', 'deployments.employee_id', '=', 'employees.id')
												->join('clients', 'deployments.client_id', '=', 'clients.id')
												->whereHas('feedbacks', function ($query) use ($currentYear){
														$query->where('rate', '>=', 7)
														->whereYear('created_at', $currentYear);
												})
												->whereRaw('(
																SELECT COUNT(DISTINCT MONTH(attendance_date))
																FROM attendances
																WHERE deployment_id = deployments.id
																				AND DAYOFWEEK(attendance_date) NOT IN (1, 7) /* Exclude Sundays (1) and Saturdays (7) */
																				AND status != "Absent" /* Exclude Absent status */
												) >= 11')
												->whereRaw('(
													SELECT COUNT(*)
													FROM attendances
													WHERE deployment_id = deployments.id
													AND status = "Absent"
												) < 10')
												->where('status','new')
            ->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

			//total number of filtered data
			$totalFiltered = Deployment::select('feedback.id as id',
											DB::raw('CONCAT(employees.last_name, ", ", employees.first_name, " ", employees.middle_name) AS full_name'), 'clients.name as company','feedback.rate')
											->join('feedback', 'deployments.id', '=', 'feedback.deployment_id')
											->join('employees', 'deployments.employee_id', '=', 'employees.id')
											->join('clients', 'deployments.client_id', '=', 'clients.id')
											->whereHas('feedbacks', function ($query) use ($currentYear){
													$query->where('rate', '>=', 7)
													->whereYear('created_at', $currentYear);
											})
										 ->whereRaw('(
												SELECT COUNT(DISTINCT MONTH(attendance_date))
												FROM attendances
												WHERE deployment_id = deployments.id
																AND DAYOFWEEK(attendance_date) NOT IN (1, 7) /* Exclude Sundays (1) and Saturdays (7) */
																AND status != "Absent" /* Exclude Absent status */
								) >= 11')->whereRaw('(
									SELECT COUNT(*)
									FROM attendances
									WHERE deployment_id = deployments.id
									AND status = "Absent"
								) < 10')->where('status','new')->count();

		} else {
			$search = $request->input('search.value');

            $posts = Deployment::select('feedback.id as id',
																DB::raw('CONCAT(employees.last_name, ", ", employees.first_name, " ", employees.middle_name) AS full_name'), 'clients.name as company','feedback.rate')
																->join('feedback', 'deployments.id', '=', 'feedback.deployment_id')
																->join('employees', 'deployments.employee_id', '=', 'employees.id')
																->join('clients', 'deployments.client_id', '=', 'clients.id')
																->whereHas('feedbacks', function ($query) use ($currentYear){
																		$query->where('rate', '>=', 7)
																		->whereYear('created_at', $currentYear);
																})
															 ->whereRaw('(
																	SELECT COUNT(DISTINCT MONTH(attendance_date))
																	FROM attendances
																	WHERE deployment_id = deployments.id
																					AND DAYOFWEEK(attendance_date) NOT IN (1, 7) /* Exclude Sundays (1) and Saturdays (7) */
																					AND status != "Absent" /* Exclude Absent status */
													) >= 11')
															->whereRaw('(
																SELECT COUNT(*)
																FROM attendances
																WHERE deployment_id = deployments.id
																AND status = "Absent"
															) < 10')
															->orWhere('employees.first_name', 'like', "%{$search}%")
															->orWhere('employees.middle_name', 'like', "%{$search}%")
															->orWhere('employees.last_name', 'like', "%{$search}%")
															->orWhere('clients.name', 'like', "%{$search}%")
															->orWhere('feedback.rate', 'like', "%{$search}%")
															->where('status','new')
															->offset($start)
															->limit($limit)
															->orderBy($order, $dir)
															->get();

			//total number of filtered data matching the search value request in the Supplier table	
			$totalFiltered = Deployment::select('feedback.id as id',
																					DB::raw('CONCAT(employees.last_name, ", ", employees.first_name, " ", employees.middle_name) AS full_name'), 'clients.name as company','feedback.rate')
																					->join('feedback', 'deployments.id', '=', 'feedback.deployment_id')
																					->join('employees', 'deployments.employee_id', '=', 'employees.id')
																					->join('clients', 'deployments.client_id', '=', 'clients.id')
																					->whereHas('feedbacks', function ($query) use ($currentYear){
																							$query->where('rate', '>=', 7)
																							->whereYear('created_at', $currentYear);
																					})
																				 ->whereRaw('(
																						SELECT COUNT(DISTINCT MONTH(attendance_date))
																						FROM attendances
																						WHERE deployment_id = deployments.id
																										AND DAYOFWEEK(attendance_date) NOT IN (1, 7) /* Exclude Sundays (1) and Saturdays (7) */
																										AND status != "Absent" /* Exclude Absent status */
																		) >= 11')
																				->whereRaw('(
																					SELECT COUNT(*)
																					FROM attendances
																					WHERE deployment_id = deployments.id
																					AND status = "Absent"
																				) < 10')
																				->where('status','new')
																				->orWhere('employees.first_name', 'like', "%{$search}%")
																				->orWhere('employees.middle_name', 'like', "%{$search}%")
																				->orWhere('employees.last_name', 'like', "%{$search}%")
																				->orWhere('clients.name', 'like', "%{$search}%")
																				->orWhere('feedback.rate', 'like', "%{$search}%")->count();
		}


		$data = array();

		if ($posts) {
			//loop posts collection to transfer in another array $nestedData
			foreach ($posts as $r) {
				$nestedData['fullname'] = $r->full_name;
				$nestedData['client_name'] = $r->company;
				$nestedData['rate'] = $r->rate;
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
