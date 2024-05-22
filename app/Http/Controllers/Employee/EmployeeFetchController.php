<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Employee;
use Validator;

class EmployeeFetchController extends Controller
{
	public function fetchEmployee(Request $request)
	{
		//column list in the table Employee
		$columns = array(
			0 => 'name',
            1 => 'nickname',
			2 => 'reference_no',
			3 => 'contact_number',
			4 => 'email',
			5 => 'address',
			6 => 'created_at',
			7 => 'action'
		);

		//get the total number of data in Employee table
		$totalData = Employee::count();
		//total number of data that will show in the datatable default 10
		$limit = $request->input('length');
		//start number for pagination ,default 0
		$start = $request->input('start');
		//order list of the column
		$order = $columns[$request->input('order.0.column')];
		//order by ,default asc 
		$dir = $request->input('order.0.dir');

		//check if user search for a value in the Employee datatable
		if (empty($request->input('search.value'))) {
			//get all the Supplier data
			$posts = Employee::offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data
			$totalFiltered = Employee::count();
		} else {
			$search = $request->input('search.value');

			$posts = Employee::where(function ($query) use ($search) {
				$query->where('name', 'like', "%{$search}%")
                    ->orWhere('nickname', 'like', "%{$search}%")
					->orWhere('reference_no', 'like', "%{$search}%")
					->orWhere('address', 'like', "%{$search}%")
					->orWhere('contact_number', 'like', "%{$search}%")
					->orWhere('email', 'like', "%{$search}%");
			})
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data matching the search value request in the Employee table	
			$totalFiltered = Employee::where(function ($query) use ($search) {
				$query->where('name', 'like', "%{$search}%")
                    ->orWhere('nickname', 'like', "%{$search}%")
					->orWhere('reference_no', 'like', "%{$search}%")
					->orWhere('address', 'like', "%{$search}%")
					->orWhere('contact_number', 'like', "%{$search}%")
					->orWhere('email', 'like', "%{$search}%");
			})
				->count();
		}


		$data = array();

		if ($posts) {
			//loop posts collection to transfer in another array $nestedData
			foreach ($posts as $r) {
				$nestedData['name'] = $r->name;
                $nestedData['nickname'] = $r->nickname;
				$nestedData['reference_no'] = $r->reference_no;
				$nestedData['contact_number'] = $r->contact_number;
				$nestedData['email'] = $r->email;
				$nestedData['address'] = $r->address;
				$nestedData['created_at'] = date('d-m-Y', strtotime($r->created_at));
				$nestedData['action'] = '
                    <button name="show" id="show" data-id="' . $r->id . '" class="btn bg-gradient-primary btn-sm">Show</button>
					<button name="edit" id="edit" data-id="' . $r->id . '" class="btn bg-gradient-warning btn-sm">Edit</button>
					<button name="delete" id="delete" data-id="' . $r->id . '" class="btn bg-gradient-danger btn-sm">Archive</button>
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

	public function fetchInactiveEmployee(Request $request)
	{
		//column list in the table Employee
		$columns = array(
			0 => 'name',
            1 => 'nickname',
			2 => 'reference_no',
			3 => 'contact_number',
			4 => 'email',
			5 => 'address',
			6 => 'created_at',
			7 => 'action'
		);

		//get the total number of data in Employee table
		$totalData = Employee::onlyTrashed()->count();
		//total number of data that will show in the datatable default 10
		$limit = $request->input('length');
		//start number for pagination ,default 0
		$start = $request->input('start');
		//order list of the column
		$order = $columns[$request->input('order.0.column')];
		//order by ,default asc 
		$dir = $request->input('order.0.dir');

		//check if user search for a value in the Employee datatable
		if (empty($request->input('search.value'))) {
			//get all the Employee data
			$posts = Employee::onlyTrashed()
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data
			$totalFiltered = Employee::onlyTrashed()->count();
		} else {
			$search = $request->input('search.value');

			$posts = Employee::onlyTrashed()
				->where(function ($query) use ($search) {
					$query->where('name', 'like', "%{$search}%")
                        ->orWhere('nickname', 'like', "%{$search}%")
						->orWhere('reference_no', 'like', "%{$search}%")
						->orWhere('address', 'like', "%{$search}%")
						->orWhere('contact_number', 'like', "%{$search}%")
						->orWhere('email', 'like', "%{$search}%");
				})
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data matching the search value request in the Employee table	
			$totalFiltered = Employee::onlyTrashed()
				->where(function ($query) use ($search) {
					$query->where('name', 'like', "%{$search}%")
                        ->orWhere('nickname', 'like', "%{$search}%")
						->orWhere('reference_no', 'like', "%{$search}%")
						->orWhere('address', 'like', "%{$search}%")
						->orWhere('contact_number', 'like', "%{$search}%")
						->orWhere('email', 'like', "%{$search}%");
				})
				->count();
		}


		$data = array();

		if ($posts) {
			//loop posts collection to transfer in another array $nestedData
			foreach ($posts as $r) {
				$nestedData['name'] = $r->name;
                $nestedData['nickname'] = $r->nickname;
				$nestedData['reference_no'] = $r->reference_no;
				$nestedData['contact_number'] = $r->contact_number;
				$nestedData['email'] = $r->email;
				$nestedData['address'] = $r->address;
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