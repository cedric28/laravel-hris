<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Client;
use Validator;

class ClientFetchController extends Controller
{
	public function fetchClient(Request $request)
	{
		//column list in the table Client
		$columns = array(
			0 => 'name',
            1 => 'short_name',
			2 => 'reference_no',
			3 => 'contact_number',
			4 => 'email',
			5 => 'address',
			6 => 'created_at',
			7 => 'action'
		);

		//get the total number of data in Client table
		$totalData = Client::count();
		//total number of data that will show in the datatable default 10
		$limit = $request->input('length');
		//start number for pagination ,default 0
		$start = $request->input('start');
		//order list of the column
		$order = $columns[$request->input('order.0.column')];
		//order by ,default asc 
		$dir = $request->input('order.0.dir');

		//check if user search for a value in the Supplier datatable
		if (empty($request->input('search.value'))) {
			//get all the Supplier data
			$posts = Client::offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data
			$totalFiltered = Client::count();
		} else {
			$search = $request->input('search.value');

			$posts = Client::where(function ($query) use ($search) {
				$query->where('name', 'like', "%{$search}%")
                    ->orWhere('short_name', 'like', "%{$search}%")
					->orWhere('reference_no', 'like', "%{$search}%")
					->orWhere('address', 'like', "%{$search}%")
					->orWhere('contact_number', 'like', "%{$search}%")
					->orWhere('email', 'like', "%{$search}%");
			})
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data matching the search value request in the Client table	
			$totalFiltered = Client::where(function ($query) use ($search) {
				$query->where('name', 'like', "%{$search}%")
                    ->orWhere('short_name', 'like', "%{$search}%")
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
                $nestedData['short_name'] = $r->short_name;
				$nestedData['reference_no'] = $r->reference_no;
				$nestedData['contact_number'] = $r->contact_number;
				$nestedData['email'] = $r->email;
				$nestedData['address'] = $r->address;
				$nestedData['created_at'] = date('d-m-Y', strtotime($r->created_at));
				$nestedData['action'] = '
                    <button name="show" id="show" data-id="' . $r->id . '" class="btn bg-gradient-primary btn-sm"><i class="fas fa-eye"></i></button>
					<button name="edit" id="edit" data-id="' . $r->id . '" class="btn bg-gradient-warning btn-sm"><i class="fas fa-pencil-alt"></i></button>
					<button name="delete" id="delete" data-id="' . $r->id . '" class="btn bg-gradient-danger btn-sm"><i class="fas fa-trash"></i></button>
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

	public function fetchInactiveClient(Request $request)
	{
		//column list in the table Client
		$columns = array(
			0 => 'name',
            1 => 'short_name',
			2 => 'reference_no',
			3 => 'contact_number',
			4 => 'email',
			5 => 'address',
			6 => 'created_at',
			7 => 'action'
		);

		//get the total number of data in Client table
		$totalData = Client::onlyTrashed()->count();
		//total number of data that will show in the datatable default 10
		$limit = $request->input('length');
		//start number for pagination ,default 0
		$start = $request->input('start');
		//order list of the column
		$order = $columns[$request->input('order.0.column')];
		//order by ,default asc 
		$dir = $request->input('order.0.dir');

		//check if user search for a value in the Supplier datatable
		if (empty($request->input('search.value'))) {
			//get all the Supplier data
			$posts = Client::onlyTrashed()
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data
			$totalFiltered = Client::onlyTrashed()->count();
		} else {
			$search = $request->input('search.value');

			$posts = Client::onlyTrashed()
				->where(function ($query) use ($search) {
					$query->where('name', 'like', "%{$search}%")
                        ->orWhere('short_name', 'like', "%{$search}%")
						->orWhere('reference_no', 'like', "%{$search}%")
						->orWhere('address', 'like', "%{$search}%")
						->orWhere('contact_number', 'like', "%{$search}%")
						->orWhere('email', 'like', "%{$search}%");
				})
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data matching the search value request in the Client table	
			$totalFiltered = Client::onlyTrashed()
				->where(function ($query) use ($search) {
					$query->where('name', 'like', "%{$search}%")
                        ->orWhere('short_name', 'like', "%{$search}%")
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
                $nestedData['short_name'] = $r->short_name;
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