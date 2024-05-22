<?php

namespace App\Http\Controllers\Regularization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Feedback;
use Validator, Hash, DB;
use Carbon\Carbon;

class RegularizationFetchController extends Controller
{
 public function fetchForRegularization(Request $request)
	{
        $currentMonth = Carbon::now()->month;
		//column list in the table Prpducts
		$columns = array(
			0 => 'fullname',
			1 => 'client_name',
			2 => 'rate',
			3 => 'action'
		);

		//get the total number of data in User table
		$totalData = Feedback::whereMonth('created_at', $currentMonth)->where('rate','>=',7)->count();
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
            $posts = Feedback::select('feedback.id as id','employees.name as fullname', 'clients.name as company','feedback.rate')
            ->join('deployments', 'deployments.id', '=', 'feedback.deployment_id')
            ->join('employees', 'deployments.employee_id', '=', 'employees.id')
            ->join('clients', 'deployments.client_id', '=', 'clients.id')
            ->whereMonth('feedback.created_at', $currentMonth)
            ->where('rate','>=',7)
            ->where([
                ['deployments.status', '=', 'new'],
            ])
            ->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

			//total number of filtered data
			$totalFiltered = Feedback::select('feedback.id as id','employees.name as fullname', 'clients.name as company','feedback.rate')
            ->join('deployments', 'deployments.id', '=', 'feedback.deployment_id')
            ->join('employees', 'deployments.employee_id', '=', 'employees.id')
            ->join('clients', 'deployments.client_id', '=', 'clients.id')
            ->whereMonth('feedback.created_at', $currentMonth)
            ->where('rate','=',10)
            ->where([
                ['deployments.status', '=', 'new'],
            ])->count();

		} else {
			$search = $request->input('search.value');

            $posts = Feedback::select('feedback.id as id','employees.name as fullname', 'clients.name as company','feedback.rate')
                ->join('employees', 'deployments.employee_id', '=', 'employees.id')
                ->join('clients', 'deployments.client_id', '=', 'clients.id')
                ->join('feedback', 'deployments.id', '=', 'feedback.deployment_id')
                ->orWhere('employees.name', 'like', "%{$search}%")
																->orWhere('clients.name', 'like', "%{$search}%")
																->orWhere('feedback.rate', 'like', "%{$search}%")
                ->whereMonth('feedback.created_at', $currentMonth)
                ->where('rate','>=',7)
																->where([
																			['deployments.status', '=', 'new'],
																])
															->offset($start)
															->limit($limit)
															->orderBy($order, $dir)
															->get();

			//total number of filtered data matching the search value request in the Supplier table	
			$totalFiltered = Feedback::select('feedback.id as id','employees.name as fullname', 'clients.name as company','feedback.rate')
                    ->join('employees', 'deployments.employee_id', '=', 'employees.id')
                    ->join('clients', 'deployments.client_id', '=', 'clients.id')
                    ->join('feedback', 'deployments.id', '=', 'feedback.deployment_id')
                    ->orWhere('employees.name', 'like', "%{$search}%")
                    ->orWhere('clients.name', 'like', "%{$search}%")
                    ->orWhere('feedback.rate', 'like', "%{$search}%")
                    ->whereMonth('feedback.created_at', $currentMonth)
                    ->where('rate','>=',7)
                    ->where([
                        ['deployments.status', '=', 'new'],
                    ])
				    ->count();
		}


		$data = array();

		if ($posts) {
			//loop posts collection to transfer in another array $nestedData
			foreach ($posts as $r) {
				$nestedData['fullname'] = $r->fullname;
				$nestedData['client_name'] = $r->company;
				$nestedData['rate'] = $r->rate;
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
