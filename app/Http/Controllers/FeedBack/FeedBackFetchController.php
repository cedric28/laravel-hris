<?php

namespace App\Http\Controllers\FeedBack;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Feedback;
use Validator, Hash, DB;
use Carbon\Carbon;

class FeedBackFetchController extends Controller
{
	public function fetchFeedBack(Request $request)
	{
        $currentYear = Carbon::now()->year;
		//column list in the table Prpducts
		$columns = array(
			0 => 'full_name',
			1 => 'client_name',
			2 => 'rate',
			3 => 'action'
		);

		//get the total number of data in User table
		$totalData = DB::table('feedback as feedback1')
		->select('feedback1.id as feedbackId', 
		DB::raw("CONCAT(employees.last_name, ', ', employees.first_name, ' ', employees.middle_name) AS full_name"), 
		'clients.name as company', 
		'feedback1.rate')
->join('deployments', 'deployments.id', '=', 'feedback1.deployment_id')
->join('employees', 'deployments.employee_id', '=', 'employees.id')
->join('clients', 'deployments.client_id', '=', 'clients.id')
->whereYear('feedback1.created_at', $currentYear)
->where([
				['deployments.status', '=', 'new'],
])
->whereNull('feedback1.deleted_at')->count();
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
            $posts = DB::table('feedback as feedback1')
														->select('feedback1.id as feedbackId', 
														DB::raw("CONCAT(employees.last_name, ', ', employees.first_name, ' ', employees.middle_name) AS full_name"), 
														'clients.name as company', 
														'feedback1.rate')
            ->join('deployments', 'deployments.id', '=', 'feedback1.deployment_id')
            ->join('employees', 'deployments.employee_id', '=', 'employees.id')
            ->join('clients', 'deployments.client_id', '=', 'clients.id')
            ->whereYear('feedback1.created_at', $currentYear)
            ->where([
                ['deployments.status', '=', 'new'],
            ])
												->whereNull('feedback1.deleted_at')
            ->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

			//total number of filtered data
			$totalFiltered = DB::table('feedback as feedback1')
												->select('feedback1.id as feedbackId', 
												DB::raw("CONCAT(employees.last_name, ', ', employees.first_name, ' ', employees.middle_name) AS full_name"), 
												'clients.name as company', 
												'feedback1.rate')
            ->join('deployments', 'deployments.id', '=', 'feedback1.deployment_id')
            ->join('employees', 'deployments.employee_id', '=', 'employees.id')
            ->join('clients', 'deployments.client_id', '=', 'clients.id')
            ->whereYear('feedback1.created_at', $currentYear)
            ->where([
                ['deployments.status', '=', 'new'],
            ])->whereNull('feedback1.deleted_at')->count();

		} else {
			$search = $request->input('search.value');

            $posts = DB::table('feedback as feedback1')
												->select('feedback1.id as feedbackId', 
												DB::raw("CONCAT(employees.last_name, ', ', employees.first_name, ' ', employees.middle_name) AS full_name"), 
												'clients.name as company', 
												'feedback1.rate')
												->join('deployments', 'deployments.id', '=', 'feedback1.deployment_id')
												->join('employees', 'deployments.employee_id', '=', 'employees.id')
												->join('clients', 'deployments.client_id', '=', 'clients.id')
                ->orWhere('employees.first_name', 'like', "%{$search}%")
																->orWhere('employees.middle_name', 'like', "%{$search}%")
																->orWhere('employees.last_name', 'like', "%{$search}%")
												->orWhere('clients.name', 'like', "%{$search}%")
												->orWhere('feedback1.rate', 'like', "%{$search}%")
												->whereYear('feedback1.created_at', $currentYear)
												->where([
														['deployments.status', '=', 'new'],
												])
												->whereNull('feedback1.deleted_at')
												->offset($start)
												->limit($limit)
												->orderBy($order, $dir)
												->get();

			//total number of filtered data matching the search value request in the Supplier table	
			$totalFiltered = DB::table('feedback as feedback1')
																				->select('feedback1.id as feedbackId', 
																				DB::raw("CONCAT(employees.last_name, ', ', employees.first_name, ' ', employees.middle_name) AS full_name"), 
																				'clients.name as company', 
																				'feedback1.rate')
                   	->join('deployments', 'deployments.id', '=', 'feedback1.deployment_id')
																				->join('employees', 'deployments.employee_id', '=', 'employees.id')
																				->join('clients', 'deployments.client_id', '=', 'clients.id')
                    ->orWhere('employees.first_name', 'like', "%{$search}%")
																				->orWhere('employees.middle_name', 'like', "%{$search}%")
																				->orWhere('employees.last_name', 'like', "%{$search}%")
                    ->orWhere('clients.name', 'like', "%{$search}%")
                    ->orWhere('feedback1.rate', 'like', "%{$search}%")
                    ->whereYear('feedback1.created_at', $currentYear)
                    ->where([
                        ['deployments.status', '=', 'new'],
                    ])
																				->whereNull('feedback1.deleted_at')
				    ->count();
		}


		$data = array();

		if ($posts) {
			//loop posts collection to transfer in another array $nestedData
			foreach ($posts as $r) {
				$nestedData['full_name'] = $r->full_name;
				$nestedData['client_name'] = $r->company;
				$nestedData['rate'] = $r->rate;
				$nestedData['action'] = '
						<button name="edit" id="edit" data-id="' . $r->feedbackId . '" class="btn bg-gradient-warning btn-sm"><i class="fas fa-pencil-alt"></i></button>
						<button name="delete" id="delete" data-id="' . $r->feedbackId . '" class="btn bg-gradient-danger btn-sm"><i class="fas fa-file-archive"></i></button>
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

	public function fetchInactiveFeedBack(Request $request)
    {
        $currentYear = Carbon::now()->year;
		//column list in the table Prpducts
		$columns = array(
			0 => 'full_name',
			1 => 'client_name',
			2 => 'rate',
			3 => 'action'
		);

		//get the total number of data in User table
        
		$totalData = DB::table('feedback as feedback1')->whereYear('feedback1.created_at', $currentYear)->count();
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
            $posts = DB::table('feedback as feedback1')
												->select('feedback1.id as feedbackId', 
												DB::raw("CONCAT(employees.last_name, ', ', employees.first_name, ' ', employees.middle_name) AS full_name"), 
												'clients.name as company', 
												'feedback1.rate')
            ->join('deployments', 'deployments.id', '=', 'feedback1.deployment_id')
            ->join('employees', 'deployments.employee_id', '=', 'employees.id')
            ->join('clients', 'deployments.client_id', '=', 'clients.id')
            ->whereYear('feedback1.created_at', $currentYear)
            ->where([
															['feedback1.deleted_at','<>', null],
                ['deployments.status', '=', 'new'],
            ])
            ->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

			//total number of filtered data
			$totalFiltered = DB::table('feedback as feedback1')
   									->select('feedback1.id as feedbackId', 
             DB::raw("CONCAT(employees.last_name, ', ', employees.first_name, ' ', employees.middle_name) AS full_name"), 
             'clients.name as company', 
             'feedback1.rate')
            ->join('deployments', 'deployments.id', '=', 'feedback1.deployment_id')
            ->join('employees', 'deployments.employee_id', '=', 'employees.id')
            ->join('clients', 'deployments.client_id', '=', 'clients.id')
            ->whereYear('feedback1.created_at', $currentYear)
            ->where([
													['feedback1.deleted_at','<>', null],
														['deployments.status', '=', 'new'],
										])->count();

		} else {
			$search = $request->input('search.value');

            $posts = DB::table('feedback as feedback1')
    								->select('feedback1.id as feedbackId', 
             DB::raw("CONCAT(employees.last_name, ', ', employees.first_name, ' ', employees.middle_name) AS full_name"), 
             'clients.name as company', 
             'feedback1.rate')
																->join('deployments', 'deployments.id', '=', 'feedback1.deployment_id')
                ->join('employees', 'deployments.employee_id', '=', 'employees.id')
                ->join('clients', 'deployments.client_id', '=', 'clients.id')
																->orWhere('employees.first_name', 'like', "%{$search}%")
																->orWhere('employees.middle_name', 'like', "%{$search}%")
																->orWhere('employees.last_name', 'like', "%{$search}%")
															->orWhere('clients.name', 'like', "%{$search}%")
															->orWhere('feedback1.rate', 'like', "%{$search}%")
                ->whereYear('feedback1.created_at', $currentYear)
																->where([
																	['feedback1.deleted_at','<>', null],
																		['deployments.status', '=', 'new'],
														])
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data matching the search value request in the Supplier table	
			$totalFiltered = DB::table('feedback as feedback1')
																				->select('feedback1.id as feedbackId', 
																				DB::raw("CONCAT(employees.last_name, ', ', employees.first_name, ' ', employees.middle_name) AS full_name"), 
																				'clients.name as company', 
																				'feedback1.rate')
																				->join('deployments', 'deployments.id', '=', 'feedback1.deployment_id')
																				->join('employees', 'deployments.employee_id', '=', 'employees.id')
																				->join('clients', 'deployments.client_id', '=', 'clients.id')
																				->orWhere('employees.first_name', 'like', "%{$search}%")
																				->orWhere('employees.middle_name', 'like', "%{$search}%")
																				->orWhere('employees.last_name', 'like', "%{$search}%")
                    ->orWhere('clients.name', 'like', "%{$search}%")
                    ->orWhere('feedback1.rate', 'like', "%{$search}%")
                    ->whereYear('feedback1.created_at', $currentYear)
																				->where([
																					['feedback1.deleted_at','<>', null],
																						['deployments.status', '=', 'new'],
																		])
				    ->count();
		}


		$data = array();

		if ($posts) {
			//loop posts collection to transfer in another array $nestedData
			foreach ($posts as $r) {
				$nestedData['full_name'] = $r->full_name;
				$nestedData['client_name'] = $r->company;
				$nestedData['rate'] = $r->rate;
				$nestedData['action'] = '
                <button name="restore" id="restore" data-id="' . $r->feedbackId . '" class="btn bg-gradient-success btn-sm">Restore</button>
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

