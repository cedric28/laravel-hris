<?php

namespace App\Http\Controllers\Discount;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Discount;

class DiscountFetchController extends Controller
{
	public function fetchDiscount(Request $request)
	{
		//column list in the table Prpducts
		$columns = array(
			0 => 'discount_name',
			1 => 'discount_rate',
			2 => 'created_at',
			3 => 'action'
		);

		//get the total number of data in Discount table
		$totalData = Discount::count();
		//total number of data that will show in the datatable default 10
		$limit = $request->input('length');
		//start number for pagination ,default 0
		$start = $request->input('start');
		//order list of the column
		$order = $columns[$request->input('order.0.column')];
		//order by ,default asc 
		$dir = $request->input('order.0.dir');

		//check if user search for a value in the discount datatable
		if (empty($request->input('search.value'))) {
			//get all the discount data
			$posts = Discount::offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data
			$totalFiltered = Discount::count();
		} else {
			$search = $request->input('search.value');

			$posts = Discount::where('discount_name', 'like', "%{$search}%")
				->orWhere('discount_rate', 'like', "%{$search}%")
				->orWhere('created_at', 'like', "%{$search}%")
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data matching the search value request in the Category table	
			$totalFiltered = Discount::where('discount_name', 'like', "%{$search}%")
				->orWhere('discount_rate', 'like', "%{$search}%")
				->orWhere('created_at', 'like', "%{$search}%")
				->count();
		}


		$data = array();

		if ($posts) {
			//loop posts collection to transfer in another array $nestedData
			foreach ($posts as $r) {
				$nestedData['discount_name'] = $r->discount_name;
				$nestedData['discount_rate'] = $r->discount_rate;
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

	public function fetchInactiveDiscount(Request $request)
	{
		//column list in the table Prpducts
		$columns = array(
			0 => 'discount_name',
			1 => 'discount_rate',
			2 => 'created_at',
			3 => 'action'
		);

		//get the total number of data in Discount table
		$totalData = Discount::onlyTrashed()->count();
		//total number of data that will show in the datatable default 10
		$limit = $request->input('length');
		//start number for pagination ,default 0
		$start = $request->input('start');
		//order list of the column
		$order = $columns[$request->input('order.0.column')];
		//order by ,default asc 
		$dir = $request->input('order.0.dir');

		//check if user search for a value in the discount datatable
		if (empty($request->input('search.value'))) {
			//get all the discount data
			$posts = Discount::onlyTrashed()
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data
			$totalFiltered = Discount::onlyTrashed()->count();
		} else {
			$search = $request->input('search.value');

			$posts = Discount::onlyTrashed()
				->orWhere('discount_name', 'like', "%{$search}%")
				->orWhere('discount_rate', 'like', "%{$search}%")
				->orWhere('created_at', 'like', "%{$search}%")
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data matching the search value request in the Category table	
			$totalFiltered = Discount::onlyTrashed()
				->orWhere('discount_name', 'like', "%{$search}%")
				->orWhere('discount_rate', 'like', "%{$search}%")
				->orWhere('created_at', 'like', "%{$search}%")
				->count();
		}


		$data = array();

		if ($posts) {
			//loop posts collection to transfer in another array $nestedData
			foreach ($posts as $r) {
				$nestedData['discount_name'] = $r->discount_name;
				$nestedData['discount_rate'] = $r->discount_rate;
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

	public function fetchNormalDiscounts()
	{
		try {
			$normalDiscounts = Discount::all();

			return response()->json([
				'normalDiscounts' => $normalDiscounts,
				'message' => 'Normal Discounts Data Fetch Successful'
			], 200);
		} catch (\Exception $e) {
			return response()->json([
				'data' => $e->getMessage(),
				'message' => 'Normal Discounts Data Fetch Failed'
			], 500);
		}
	}
}
