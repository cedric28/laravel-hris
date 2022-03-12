<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Customer;
use App\Point;
use App\CustomerPoint;
use Validator;

class CustomerFetchController extends Controller
{
	public function fetchCustomer(Request $request)
	{
		//column list in the table Customer
		$columns = array(
			0 => 'name',
			1 => 'reference_no',
			2 => 'contact_number',
			3 => 'email',
			4 => 'address',
			5 => 'created_at',
			6 => 'action'
		);

		//get the total number of data in Customer table
		$totalData = Customer::count();
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
			$posts = Customer::offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data
			$totalFiltered = Customer::count();
		} else {
			$search = $request->input('search.value');

			$posts = Customer::where('name', 'like', "%{$search}%")
				->orWhere('reference_no', 'like', "%{$search}%")
				->orWhere('address', 'like', "%{$search}%")
				->orWhere('contact_number', 'like', "%{$search}%")
				->orWhere('email', 'like', "%{$search}%")
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data matching the search value request in the Customer table	
			$totalFiltered = Customer::where('name', 'like', "%{$search}%")
				->orWhere('reference_no', 'like', "%{$search}%")
				->orWhere('address', 'like', "%{$search}%")
				->orWhere('contact_number', 'like', "%{$search}%")
				->orWhere('email', 'like', "%{$search}%")
				->count();
		}


		$data = array();

		if ($posts) {
			//loop posts collection to transfer in another array $nestedData
			foreach ($posts as $r) {
				$nestedData['name'] = $r->name;
				$nestedData['reference_no'] = $r->reference_no;
				$nestedData['contact_number'] = $r->contact_number;
				$nestedData['email'] = $r->email;
				$nestedData['address'] = $r->address;
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

	public function getCustomerInfo(Request $request)
	{
		try {
			//validate request value
			$validator = Validator::make($request->all(), [
				'reference_no' => 'required',
			]);

			if ($validator->fails()) {
				return response()->json([
					'data' => $validator->errors(),
					'message' => 'Customer Info Data Not Found'
				], 422);
			}

			$search = $request->reference_no;
			$accountType = $request->account_type;

			$customerInfo = Customer::where('reference_no', 'like', "%{$search}%")->first();

			if (!$customerInfo) {
				return response()->json([
					'message' => 'Customer Information not found'
				], 404);
			} else {
				$totalPoints = $customerInfo->customer_points->sum('point');
				$pointSetting = Point::all();
				if ($accountType == "discount") {
					if ($totalPoints == $pointSetting[0]->total_needed_point) {
						return response()->json([
							'customerInfo' => $customerInfo,
							'discount' => $pointSetting,
							'status' => 'success',
							'message' => 'Customer Info Data Fetch Successful'
						], 200);
					} else {
						return response()->json([
							'status' => "error",
							'balance' => $totalPoints,
							'message' => 'Insufficient funds'
						], 200);
					}
				} else {
					return response()->json([
						'customerInfo' => $customerInfo,
						'balance' => $totalPoints,
						'status' => 'success',
						'message' => 'Customer Info Data Fetch Successful'
					], 200);
				}
			}
		} catch (\Exception $e) {
			//if error occurs rollback the data from it's previos state
			return response()->json([
				'data' => $e->getMessage(),
				'message' => 'Customer Info Data Fetch Failed'
			], 500);
		}
	}

	public function getCustomerPoints(Request $request)
	{
		$customerId = $request->customer_id;
		//column list in the table Customer
		$columns = array(
			0 => 'point',
			1 => 'created_at'
		);

		//get the total number of data in Customer table
		$totalData = CustomerPoint::where('customer_id', $customerId)->count();
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
			$posts = CustomerPoint::where('customer_id', $customerId)
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data
			$totalFiltered = CustomerPoint::where('customer_id', $customerId)->count();
		} else {
			$search = $request->input('search.value');

			$posts = CustomerPoint::where('customer_id', $customerId)
				->orWhere('created_at', 'like', "%{$search}%")
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data matching the search value request in the Customer table	
			$totalFiltered = CustomerPoint::where('customer_id', $customerId)
				->orWhere('created_at', 'like', "%{$search}%")
				->count();
		}


		$data = array();

		if ($posts) {
			//loop posts collection to transfer in another array $nestedData
			foreach ($posts as $r) {
				$nestedData['point'] = $r->point;
				$nestedData['created_at'] = date('d-m-Y', strtotime($r->created_at));
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
