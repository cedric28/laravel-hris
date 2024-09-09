<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Payroll;

class PayrollFetchController extends Controller
{
    public function fetchPayroll(Request $request)
	{
		//column list in the table Client
		$columns = array(
            0 => 'end_date',
            1 => 'start_date',
            2 => 'description',
			3 => 'created_at',
			4 => 'action'
		);

		//get the total number of data in Client table
		$totalData = Payroll::count();
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
			$posts = Payroll::offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data
			$totalFiltered = Payroll::count();
		} else {
			$search = $request->input('search.value');

			$posts = Payroll::where(function ($query) use ($search) {
				$query->where('description', 'like', "%{$search}%")
				->orWhere('start_date', 'like', "%{$search}%")
                    ->orWhere('end_date', 'like', "%{$search}%");
			    })
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data matching the search value request in the Client table	
			$totalFiltered = Payroll::where(function ($query) use ($search) {
				$query->where('description', 'like', "%{$search}%")
				->orWhere('start_date', 'like', "%{$search}%")
                    ->orWhere('end_date', 'like', "%{$search}%");
			    })
				->count();
		}


		$data = array();

		if ($posts) {
			//loop posts collection to transfer in another array $nestedData
			foreach ($posts as $r) {
				$nestedData['description'] = $r->description;
				$nestedData['start_date'] = date('M d, Y', strtotime($r->start_date));
                $nestedData['end_date'] = date('M d, Y', strtotime($r->end_date));
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

	public function fetchInactivePayroll(Request $request)
	{
		//column list in the table Client
        $columns = array(
            0 => 'end_date',
            1 => 'start_date',
            2 => 'description',
			3 => 'created_at',
			4 => 'action'
		);

		//get the total number of data in Client table
		$totalData = Payroll::onlyTrashed()->count();
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
			$posts = Payroll::onlyTrashed()
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data
			$totalFiltered = Payroll::onlyTrashed()->count();
		} else {
			$search = $request->input('search.value');

			$posts = Payroll::onlyTrashed()
				->where(function ($query) use ($search) {
					$query->where('description', 'like', "%{$search}%")
				    ->orWhere('start_date', 'like', "%{$search}%")
                    ->orWhere('end_date', 'like', "%{$search}%");
			    })
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data matching the search value request in the Client table	
			$totalFiltered = Client::onlyTrashed()
				->where(function ($query) use ($search) {
					$query->where('description', 'like', "%{$search}%")
				    ->orWhere('start_date', 'like', "%{$search}%")
                    ->orWhere('end_date', 'like', "%{$search}%");
			    })
				->count();
		}


		$data = array();

		if ($posts) {
			//loop posts collection to transfer in another array $nestedData
			foreach ($posts as $r) {
                $nestedData['description'] = $r->description;
				$nestedData['start_date'] = date('M d, Y', strtotime($r->start_date));
                $nestedData['end_date'] = date('M d, Y', strtotime($r->end_date));
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
