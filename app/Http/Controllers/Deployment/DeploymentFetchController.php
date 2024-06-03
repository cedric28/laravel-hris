<?php

namespace App\Http\Controllers\Deployment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Deployment;

class DeploymentFetchController extends Controller
{
	public function fetchDeployment(Request $request)
	{
		//column list in the table Prpducts
		$columns = array(
			0 => 'first_name',
			1 => 'client_name',
			2 => 'position',
			3 => 'status',
			4 => 'start_date',
			5 => 'end_date',
			6 => 'action'
		);

		//get the total number of data in User table
		$totalData = Deployment::count();
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
			$posts = Deployment::select('employees.first_name as first_name','employees.middle_name as middle_name','employees.last_name as last_name','clients.name as client_name','deployments.position','deployments.status','deployments.start_date','deployments.end_date', 'deployments.id as id')
				->join('employees', 'deployments.employee_id', '=', 'employees.id')
				->join('clients', 'deployments.client_id', '=', 'clients.id')
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data
			$totalFiltered = Deployment::count();
		} else {
			$search = $request->input('search.value');

			$posts = Deployment::where(function ($query) use ($search) {
				$query->whereHas('employee', function ($query) use ($search) {
					$query->where('first_name', 'like', "%{$search}%")
					->orWhere('middle_name', 'like', "%{$search}%")
					->orWhere('last_name', 'like', "%{$search}%");
				})
				->whereHas('client', function ($query) use ($search) {
					$query->where('name', 'like', "%{$search}%");
				})
					->orWhere('position', 'like', "%{$search}%")
					->orWhere('status', 'like', "%{$search}%")
					->orWhere('start_date', 'like', "%{$search}%")
					->orWhere('end_date', 'like', "%{$search}%");
			})
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data matching the search value request in the Supplier table	
			$totalFiltered = Deployment::where(function ($query) use ($search) {
				$query->whereHas('employee', function ($query) use ($search) {
					$query->where('first_name', 'like', "%{$search}%")
					->orWhere('middle_name', 'like', "%{$search}%")
					->orWhere('last_name', 'like', "%{$search}%");
				})
				->whereHas('client', function ($query) use ($search) {
					$query->where('name', 'like', "%{$search}%");
				})
					->orWhere('position', 'like', "%{$search}%")
					->orWhere('status', 'like', "%{$search}%")
					->orWhere('start_date', 'like', "%{$search}%")
					->orWhere('end_date', 'like', "%{$search}%");
			})
				->count();
		}


		$data = array();

		if ($posts) {
			//loop posts collection to transfer in another array $nestedData
			foreach ($posts as $r) {
				$nestedData['employee_name'] = $r->last_name.', '.$r->first_name. ' '.$r->middle_name;
				$nestedData['client_name'] = $r->client_name;
				$nestedData['position'] = $r->position;
				$nestedData['status'] = ucwords($r->status);
				$nestedData['start_date'] = date('m-d-Y', strtotime($r->start_date));
				$nestedData['end_date'] = date('m-d-Y', strtotime($r->end_date));
				if($r->status != 'new'){
					$nestedData['action'] = '
						<button name="show" id="show" data-id="' . $r->id . '" class="btn bg-gradient-primary btn-sm"><i class="nav-icon fas fa-eye"></i></button>
						<button name="edit" id="edit" data-id="' . $r->id . '" class="btn bg-gradient-warning btn-sm"><i class="fas fa-pencil-alt"></i></button>
						<button name="delete" id="delete" data-id="' . $r->id . '" class="btn bg-gradient-danger btn-sm"><i class="fas fa-file-archive"></i></button>
					';
				} else {
					$nestedData['action'] = '
					<button name="work-details" id="work-details" data-id="' . $r->id . '" class="btn bg-gradient-success btn-sm"><i class="fas fa-book-reader"></i></button>
						<button name="show" id="show" data-id="' . $r->id . '" class="btn bg-gradient-primary btn-sm"><i class="nav-icon fas fa-eye"></i></button>
						<button name="edit" id="edit" data-id="' . $r->id . '" class="btn bg-gradient-warning btn-sm"><i class="fas fa-pencil-alt"></i></button>
						<button name="delete" id="delete" data-id="' . $r->id . '" class="btn bg-gradient-danger btn-sm"><i class="fas fa-file-archive"></i></button>
					';
				}
			
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

	public function fetchInactiveDeployment(Request $request)
	{
	//column list in the table Prpducts
	$columns = array(
		0 => 'first_name',
		1 => 'client_name',
		2 => 'position',
		3 => 'status',
		4 => 'start_date',
		5 => 'end_date',
		6 => 'action'
	);

	//get the total number of data in User table
	$totalData = Deployment::onlyTrashed()->count();
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
		$posts = Deployment::onlyTrashed()->select('employees.first_name as first_name','employees.middle_name as middle_name','employees.last_name as last_name','clients.name as client_name','deployments.position','deployments.status','deployments.start_date','deployments.end_date', 'deployments.id as id')
			->join('employees', 'deployments.employee_id', '=', 'employees.id')
			->join('clients', 'deployments.client_id', '=', 'clients.id')
			->offset($start)
			->limit($limit)
			->orderBy($order, $dir)
			->get();

		//total number of filtered data
		$totalFiltered = Deployment::onlyTrashed()->count();
	} else {
		$search = $request->input('search.value');

		$posts = Deployment::onlyTrashed()->where(function ($query) use ($search) {
			$query->whereHas('employee', function ($query) use ($search) {
				$query->where('first_name', 'like', "%{$search}%")
				->orWhere('middle_name', 'like', "%{$search}%")
				->orWhere('last_name', 'like', "%{$search}%");
			})
			->whereHas('client', function ($query) use ($search) {
				$query->where('name', 'like', "%{$search}%");
			})
				->orWhere('position', 'like', "%{$search}%")
				->orWhere('status', 'like', "%{$search}%")
				->orWhere('start_date', 'like', "%{$search}%")
				->orWhere('end_date', 'like', "%{$search}%");
		})
			->offset($start)
			->limit($limit)
			->orderBy($order, $dir)
			->get();

		//total number of filtered data matching the search value request in the Supplier table	
		$totalFiltered = Deployment::onlyTrashed()->where(function ($query) use ($search) {
			$query->whereHas('employee', function ($query) use ($search) {
				$query->where('first_name', 'like', "%{$search}%")
					->orWhere('middle_name', 'like', "%{$search}%")
					->orWhere('last_name', 'like', "%{$search}%");
			})
			->whereHas('client', function ($query) use ($search) {
				$query->where('name', 'like', "%{$search}%");
			})
				->orWhere('position', 'like', "%{$search}%")
				->orWhere('status', 'like', "%{$search}%")
				->orWhere('start_date', 'like', "%{$search}%")
				->orWhere('end_date', 'like', "%{$search}%");
		})
			->count();
	}


	$data = array();

		if ($posts) {
			//loop posts collection to transfer in another array $nestedData
			foreach ($posts as $r) {

				$nestedData['employee_name'] = $r->last_name.', '.$r->first_name. ' '.$r->middle_name;
				$nestedData['client_name'] = $r->client_name;
				$nestedData['position'] = $r->position;
				$nestedData['status'] = ucwords($r->status);
				$nestedData['start_date'] = date('m-d-Y', strtotime($r->start_date));
				$nestedData['end_date'] = date('m-d-Y', strtotime($r->end_date));
				$nestedData['action'] = '
						<button name="restore" id="restore" data-id="' . $r->id . '" class="btn bg-gradient-success btn-sm">Restore</button>
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

