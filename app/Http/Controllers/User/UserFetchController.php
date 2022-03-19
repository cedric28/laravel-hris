<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;

class UserFetchController extends Controller
{
	public function fetchUser(Request $request)
	{
		//column list in the table Prpducts
		$columns = array(
			0 => 'first_name',
			1 => 'last_name',
			2 => 'email',
			3 => 'roles.name',
			4 => 'users.created_at',
			5 => 'action'
		);

		//get the total number of data in User table
		$totalData = User::count();
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
			$posts = User::select('*', 'users.id as id')
				->join('roles', 'users.role_id', '=', 'roles.id')
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data
			$totalFiltered = User::count();
		} else {
			$search = $request->input('search.value');

			$posts = User::where('first_name', 'like', "%{$search}%")
				->orWhere('last_name', 'like', "%{$search}%")
				->orWhere('email', 'like', "%{$search}%")
				->orWhereHas('role', function ($query) use ($search) {
					$query->where('name', 'like', "%{$search}%");
				})
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data matching the search value request in the Supplier table	
			$totalFiltered = User::where('first_name', 'like', "%{$search}%")
				->orWhere('last_name', 'like', "%{$search}%")
				->orWhere('email', 'like', "%{$search}%")
				->orWhereHas('role', function ($query) use ($search) {
					$query->where('name', 'like', "%{$search}%");
				})
				->count();
		}


		$data = array();

		if ($posts) {
			//loop posts collection to transfer in another array $nestedData
			foreach ($posts as $r) {

				$nestedData['first_name'] = $r->first_name;
				$nestedData['last_name'] = $r->last_name;
				$nestedData['email'] = $r->email;
				$nestedData['role'] = $r->role->name;
				$nestedData['created_at'] = date('d-m-Y', strtotime($r->created_at));
				$nestedData['action'] = '
						<button name="show" id="show" data-id="' . $r->id . '" class="btn bg-gradient-primary btn-sm">Show</button>
						<button name="edit" id="edit" data-id="' . $r->id . '" class="btn bg-gradient-warning btn-sm">Edit</button>
						<button name="delete" id="delete" data-id="' . $r->id . '" class="btn bg-gradient-danger btn-sm">Delete</button>
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

	public function fetchInactiveUser(Request $request)
	{
		//column list in the table Prpducts
		$columns = array(
			0 => 'first_name',
			1 => 'last_name',
			2 => 'email',
			3 => 'name',
			4 => 'users.created_at',
			5 => 'action'
		);

		//get the total number of data in User table
		$totalData = User::onlyTrashed()->count();
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
			$posts = User::onlyTrashed()
				->select('*', 'users.id as id')
				->join('roles', 'users.role_id', '=', 'roles.id')
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data
			$totalFiltered = User::onlyTrashed()->count();
		} else {
			$search = $request->input('search.value');

			$posts = User::onlyTrashed()
				->orWhere('first_name', 'like', "%{$search}%")
				->orWhere('last_name', 'like', "%{$search}%")
				->orWhere('email', 'like', "%{$search}%")
				->orWhereHas('role', function ($query) use ($search) {
					$query->where('name', 'like', "%{$search}%");
				})
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data matching the search value request in the Supplier table	
			$totalFiltered = User::onlyTrashed()
				->orWhere('first_name', 'like', "%{$search}%")
				->orWhere('last_name', 'like', "%{$search}%")
				->orWhere('email', 'like', "%{$search}%")
				->orWhereHas('role', function ($query) use ($search) {
					$query->where('name', 'like', "%{$search}%");
				})
				->count();
		}


		$data = array();

		if ($posts) {
			//loop posts collection to transfer in another array $nestedData
			foreach ($posts as $r) {

				$nestedData['first_name'] = $r->first_name;
				$nestedData['last_name'] = $r->last_name;
				$nestedData['email'] = $r->email;
				$nestedData['role'] = $r->role->name;
				$nestedData['created_at'] = date('d-m-Y', strtotime($r->created_at));
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
