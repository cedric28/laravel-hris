<?php

namespace App\Http\Controllers\GeneralDeduction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\GeneralDeduction;
use Validator;

class GeneralDeductionFetchController extends Controller
{
    public function fetchGeneralDeductions(Request $request)
	{
		//column list in the table Client
		$columns = array(
			0 => 'name',
			1 => 'amount',
			2 => 'created_at',
			3 => 'action'
		);

		//get the total number of data in Client table
		$totalData = GeneralDeduction::count();
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
			$posts = GeneralDeduction::offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data
			$totalFiltered = GeneralDeduction::count();
		} else {
			$search = $request->input('search.value');

			$posts = GeneralDeduction::where(function ($query) use ($search) {
				$query->where('name', 'like', "%{$search}%")
				->orWhere('amount', 'like', "%{$search}%");
			})
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data matching the search value request in the Client table	
			$totalFiltered = GeneralDeduction::where(function ($query) use ($search) {
				$query->where('name', 'like', "%{$search}%")
				->orWhere('amount', 'like', "%{$search}%");
			})
				->count();
		}


		$data = array();

		if ($posts) {
			//loop posts collection to transfer in another array $nestedData
			foreach ($posts as $r) {
				$nestedData['name'] = $r->name;
    $nestedData['amount'] = $r->amount;
				$nestedData['created_at'] = date('M d, Y', strtotime($r->created_at));
				$nestedData['action'] = '
					<button name="edit" id="edit" data-id="' . $r->id . '" class="btn bg-gradient-warning btn-sm"><i class="fas fa-pencil-alt"></i></button>
					<button name="delete" id="delete" data-id="' . $r->id . '" class="btn bg-gradient-danger btn-sm"><i class="fas fa-file-archive"></i></button>
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

	public function fetchInactiveGeneralDeductions(Request $request)
	{
		//column list in the table Client
		$columns = array(
			0 => 'name',
			1 => 'amount',
			2 => 'created_at',
			3 => 'action'
		);

		//get the total number of data in Client table
		$totalData = GeneralDeduction::onlyTrashed()->count();
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
			$posts = GeneralDeduction::onlyTrashed()
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data
			$totalFiltered = GeneralDeduction::onlyTrashed()->count();
		} else {
			$search = $request->input('search.value');

			$posts = GeneralDeduction::onlyTrashed()
				->where(function ($query) use ($search) {
					$query->where('name', 'like', "%{$search}%")
				        ->orWhere('amount', 'like', "%{$search}%");
				})
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data matching the search value request in the Client table	
			$totalFiltered = Client::onlyTrashed()
				->where(function ($query) use ($search) {
					$query->where('name', 'like', "%{$search}%")
				        ->orWhere('amount', 'like', "%{$search}%");
				})
				->count();
		}


		$data = array();

		if ($posts) {
			//loop posts collection to transfer in another array $nestedData
			foreach ($posts as $r) {
				$nestedData['name'] = $r->name;
    $nestedData['amount'] = $r->amount;
				$nestedData['created_at'] =date('M d, Y', strtotime($r->created_at));
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
