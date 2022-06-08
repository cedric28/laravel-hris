<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Delivery;
use App\Stock;
use App\DeliveryRequestItem;
use App\DeliveryRequest;
use App\ReturnStockItem;
use App\ReturnStock;
use Illuminate\Support\Facades\DB;

class StockFetchController extends Controller
{
	public function fetchDeliveries(Request $request)
	{
		//column list in the table Prpducts
		$columns = array(
			0 => 'reference_no',
			1 => 'received_by',
			2 => 'received_at',
			3 => 'action'
		);

		//get the total number of data in Product table
		$totalData = Delivery::count();
		//total number of data that will show in the datatable default 10
		$limit = $request->input('length');
		//start number for pagination ,default 0
		$start = $request->input('start');
		//order list of the column
		$order = $columns[$request->input('order.0.column')];
		//order by ,default asc 
		$dir = $request->input('order.0.dir');

		//check if user search for a value in the product datatable
		if (empty($request->input('search.value'))) {
			//get all the product data
			$posts = Delivery::offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data
			$totalFiltered = Delivery::count();
		} else {
			$search = $request->input('search.value');

			$posts = Delivery::where('reference_no', 'like', "%{$search}%")
				->orWhere('received_by', 'like', "%{$search}%")
				->orWhere('received_at', 'like', "%{$search}%")
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data matching the search value request in the delivery table	
			$totalFiltered = Delivery::where('reference_no', 'like', "%{$search}%")
				->orWhere('received_by', 'like', "%{$search}%")
				->orWhere('received_at', 'like', "%{$search}%")
				->count();
		}


		$data = array();

		if ($posts) {
			//loop posts collection to transfer in another array $nestedData
			foreach ($posts as $r) {
				$nestedData['reference_no'] = $r->reference_no;
				$nestedData['received_by'] = $r->received_by;
				$nestedData['received_at'] = date('d-m-Y', strtotime($r->received_at));
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

	public function fetchProductsDelivery(Request $request)
	{
		//column list in the table Prpducts
		$columns = array(
			0 => 'reference_no',
			1 => 'product_name',
			2 => 'qty',
			3 => 'expired_at',
			4 => 'received_at',
			5 => 'action'
		);

		$delivery_id = $request->delivery_id;

		//get the total number of data in Stock table
		$totalData = Stock::where([
			['delivery_id', $delivery_id],
			['deleted_at', '=', null]
		])->count();
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
			//get all the Stock data
			$posts = DB::table('stocks')
				->leftJoin('deliveries', 'stocks.delivery_id', '=', 'deliveries.id')
				->leftJoin('inventories', 'stocks.product_id', '=', 'inventories.id')
				->select('stocks.*', 'inventories.product_name', 'deliveries.reference_no', 'deliveries.received_by')
				->where([
					['stocks.delivery_id', $delivery_id],
					['stocks.deleted_at', '=', null]
				])
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data
			$totalFiltered = DB::table('stocks')
				->leftJoin('deliveries', 'stocks.delivery_id', '=', 'deliveries.id')
				->leftJoin('inventories', 'stocks.product_id', '=', 'inventories.id')
				->select('stocks.*', 'inventories.product_name', 'deliveries.reference_no', 'deliveries.received_by')
				->where([
					['stocks.delivery_id', $delivery_id],
					['stocks.deleted_at', '=', null]
				])
				->count();
		} else {
			$search = $request->input('search.value');

			$posts = DB::table('stocks')
				->leftJoin('deliveries', 'stocks.delivery_id', '=', 'deliveries.id')
				->leftJoin('inventories', 'stocks.product_id', '=', 'inventories.id')
				->select('stocks.*', 'inventories.product_name', 'deliveries.reference_no', 'deliveries.received_by')
				->orWhere('deliveries.reference_no', 'like', "%{$search}%")
				->orWhere('deliveries.received_by', 'like', "%{$search}%")
				->orWhere('inventories.product_name', 'like', "%{$search}%")
				->orWhere('stocks.qty', 'like', "%{$search}%")
				->orWhere('stocks.expired_at', 'like', "%{$search}%")
				->orWhere('deliveries.received_at', 'like', "%{$search}%")
				->where([
					['stocks.delivery_id', $delivery_id],
					['stocks.deleted_at', '=', null]
				])
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data matching the search value request in the Supplier table	
			$totalFiltered = DB::table('stocks')
				->leftJoin('deliveries', 'stocks.delivery_id', '=', 'deliveries.id')
				->leftJoin('inventories', 'stocks.product_id', '=', 'inventories.id')
				->select('stocks.*', 'inventories.product_name', 'deliveries.reference_no', 'deliveries.received_by')
				->orWhere('deliveries.reference_no', 'like', "%{$search}%")
				->orWhere('deliveries.received_by', 'like', "%{$search}%")
				->orWhere('inventories.product_name', 'like', "%{$search}%")
				->orWhere('stocks.qty', 'like', "%{$search}%")
				->orWhere('stocks.expired_at', 'like', "%{$search}%")
				->orWhere('deliveries.received_at', 'like', "%{$search}%")
				->where([
					['stocks.delivery_id', $delivery_id],
					['stocks.deleted_at', '=', null]
				])
				->count();
		}


		$data = array();

		if ($posts) {
			//loop posts collection to transfer in another array $nestedData
			foreach ($posts as $r) {
				$nestedData['reference_no'] = $r->reference_no;
				$nestedData['product_name'] = $r->product_name;
				$nestedData['qty'] = $r->qty;
				$nestedData['expired_at'] = date('m-d-Y', strtotime($r->expired_at));
				$nestedData['received_at'] = date('m-d-Y', strtotime($r->created_at));
				$nestedData['action'] = '
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

	public function fetchStockInHistory(Request $request)
	{
		//column list in the table Prpducts
		$columns = array(
			0 => 'reference_no',
			1 => 'name',
			2 => 'product_name',
			3 => 'qty',
			4 => 'received_qty',
			5 => 'defectived_qty',
			6 => 'expired_at',
			7 => 'created_at'
		);

		//get the total number of data in Delivery Items table
		$totalData = DeliveryRequestItem::count();
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
			//get all the Stock data
			$posts = DB::table('delivery_request_items')
				->leftJoin('delivery_requests', 'delivery_request_items.delivery_request_id', '=', 'delivery_requests.id')
				->leftJoin('suppliers', 'delivery_requests.supplier_id', '=', 'suppliers.id')
				->leftJoin('inventories', 'delivery_request_items.product_id', '=', 'inventories.id')
				->select('delivery_request_items.*', 'inventories.product_name', 'delivery_requests.reference_no', 'suppliers.name')
				->where([
					['delivery_request_items.deleted_at', '=', null],
					['delivery_requests.status', '=', 'completed']
				])
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data
			$totalFiltered = DB::table('delivery_request_items')
				->leftJoin('delivery_requests', 'delivery_request_items.delivery_request_id', '=', 'delivery_requests.id')
				->leftJoin('suppliers', 'delivery_requests.supplier_id', '=', 'suppliers.id')
				->leftJoin('inventories', 'delivery_request_items.product_id', '=', 'inventories.id')
				->select('delivery_request_items.*', 'inventories.product_name', 'delivery_requests.reference_no', 'suppliers.name')
				->where([
					['delivery_request_items.deleted_at', '=', null],
					['delivery_requests.status', '=', 'completed']
				])
				->count();
		} else {
			$search = $request->input('search.value');

			$posts = DB::table('delivery_request_items')
				->leftJoin('delivery_requests', 'delivery_request_items.delivery_request_id', '=', 'delivery_requests.id')
				->leftJoin('suppliers', 'delivery_requests.supplier_id', '=', 'suppliers.id')
				->leftJoin('inventories', 'delivery_request_items.product_id', '=', 'inventories.id')
				->select('delivery_request_items.*', 'inventories.product_name', 'delivery_requests.reference_no', 'suppliers.name')
				->where(function ($query) use ($search) {
					$query->where('delivery_requests.reference_no', 'like', '%' . $search . '%')
						->orWhere('suppliers.name', 'like', "%{$search}%")
						->orWhere('inventories.product_name', 'like', "%{$search}%")
						->orWhere('delivery_request_items.qty', 'like', "%{$search}%")
						->orWhere('delivery_request_items.received_qty', 'like', "%{$search}%")
						->orWhere('delivery_request_items.defectived_qty', 'like', "%{$search}%")
						->orWhere('delivery_request_items.expired_at', 'like', "%{$search}%")
						->orWhere('delivery_request_items.created_at', 'like', "%{$search}%");
				})
				->Where([
					['delivery_request_items.deleted_at', '=', null],
					['delivery_requests.status', '=', 'completed']
				])
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data matching the search value request in the Supplier table	
			$totalFiltered = DB::table('delivery_request_items')
				->leftJoin('delivery_requests', 'delivery_request_items.delivery_request_id', '=', 'delivery_requests.id')
				->leftJoin('suppliers', 'delivery_requests.supplier_id', '=', 'suppliers.id')
				->leftJoin('inventories', 'delivery_request_items.product_id', '=', 'inventories.id')
				->select('delivery_request_items.*', 'inventories.product_name', 'delivery_requests.reference_no', 'suppliers.name')
				->where(function ($query) use ($search) {
					$query->where('delivery_requests.reference_no', 'like', '%' . $search . '%')
						->orWhere('suppliers.name', 'like', "%{$search}%")
						->orWhere('inventories.product_name', 'like', "%{$search}%")
						->orWhere('delivery_request_items.qty', 'like', "%{$search}%")
						->orWhere('delivery_request_items.received_qty', 'like', "%{$search}%")
						->orWhere('delivery_request_items.defectived_qty', 'like', "%{$search}%")
						->orWhere('delivery_request_items.expired_at', 'like', "%{$search}%")
						->orWhere('delivery_request_items.created_at', 'like', "%{$search}%");
				})
				->Where([
					['delivery_request_items.deleted_at', '=', null],
					['delivery_requests.status', '=', 'completed']
				])
				->count();
		}


		$data = array();

		if ($posts) {
			//loop posts collection to transfer in another array $nestedData
			foreach ($posts as $r) {
				$nestedData['reference_no'] = $r->reference_no;
				$nestedData['supplier'] = $r->name;
				$nestedData['product_name'] = $r->product_name;
				$nestedData['qty'] = $r->qty;
				$nestedData['received_qty'] = $r->received_qty;
				$nestedData['defective_qty'] = $r->defectived_qty;
				$nestedData['expired_at'] = date('d-m-Y', strtotime($r->expired_at));
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

	//fetch Product Delivery Request
	public function fetchProductsDeliveryRequest(Request $request)
	{
		//column list in the table Prpducts
		$columns = array(
			0 => 'reference_no',
			1 => 'product_name',
			2 => 'qty',
			3 => 'unit_measurement',
			4 => 'received_qty',
			5 => 'defectived_qty',
			6 => 'expired_at',
			7 => 'remark',
			8 => 'note',
			9 => 'action'
		);

		$delivery_request_id = $request->delivery_request_id;

		//get the total number of data in Stock table
		$totalData = DeliveryRequestItem::where([
			['delivery_request_id', $delivery_request_id],
			['deleted_at', '=', null]
		])->count();
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
			//get all the Stock data
			$posts = DB::table('delivery_request_items')
				->leftJoin('delivery_requests', 'delivery_request_items.delivery_request_id', '=', 'delivery_requests.id')
				->leftJoin('inventories', 'delivery_request_items.product_id', '=', 'inventories.id')
				->select('delivery_request_items.*', 'inventories.product_name', 'delivery_requests.reference_no', 'delivery_requests.delivery_at', 'delivery_requests.status')
				->Where([
					['delivery_request_items.delivery_request_id', $delivery_request_id],
					['delivery_request_items.deleted_at', '=', null]
				])
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data
			$totalFiltered = DB::table('delivery_request_items')
				->leftJoin('delivery_requests', 'delivery_request_items.delivery_request_id', '=', 'delivery_requests.id')
				->leftJoin('inventories', 'delivery_request_items.product_id', '=', 'inventories.id')
				->select('delivery_request_items.*', 'inventories.product_name', 'delivery_requests.reference_no', 'delivery_requests.delivery_at', 'delivery_requests.status')
				->Where([
					['delivery_request_items.delivery_request_id', $delivery_request_id],
					['delivery_request_items.deleted_at', '=', null]
				])
				->count();
		} else {
			$search = $request->input('search.value');

			$posts = DB::table('delivery_request_items')
				->leftJoin('delivery_requests', 'delivery_request_items.delivery_request_id', '=', 'delivery_requests.id')
				->leftJoin('inventories', 'delivery_request_items.product_id', '=', 'inventories.id')
				->select('delivery_request_items.*', 'inventories.product_name', 'delivery_requests.reference_no', 'delivery_requests.delivery_at', 'delivery_requests.status')
				->where(function ($query) use ($search) {
					$query->where('delivery_requests.reference_no', 'like', '%' . $search . '%')
						->orWhere('delivery_requests.reference_no', 'like', "%{$search}%")
						->orWhere('inventories.product_name', 'like', "%{$search}%")
						->orWhere('delivery_request_items.qty', 'like', "%{$search}%")
						->orWhere('delivery_request_items.unit_measurement', 'like', "%{$search}%")
						->orWhere('delivery_request_items.received_qty', 'like', "%{$search}%")
						->orWhere('delivery_request_items.defectived_qty', 'like', "%{$search}%")
						->orWhere('delivery_request_items.expired_at', 'like', "%{$search}%")
						->orWhere('delivery_request_items.remark', 'like', "%{$search}%")
						->orWhere('delivery_request_items.note', 'like', "%{$search}%");
				})
				->Where([
					['delivery_request_items.delivery_request_id', $delivery_request_id],
					['delivery_request_items.deleted_at', '=', null]
				])

				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data matching the search value request in the Supplier table	
			$totalFiltered = DB::table('delivery_request_items')
				->leftJoin('delivery_requests', 'delivery_request_items.delivery_request_id', '=', 'delivery_requests.id')
				->leftJoin('inventories', 'delivery_request_items.product_id', '=', 'inventories.id')
				->select('delivery_request_items.*', 'inventories.product_name', 'delivery_requests.reference_no', 'delivery_requests.delivery_at', 'delivery_requests.status')
				->where(function ($query) use ($search) {
					$query->where('delivery_requests.reference_no', 'like', '%' . $search . '%')
						->orWhere('delivery_requests.reference_no', 'like', "%{$search}%")
						->orWhere('inventories.product_name', 'like', "%{$search}%")
						->orWhere('delivery_request_items.qty', 'like', "%{$search}%")
						->orWhere('delivery_request_items.unit_measurement', 'like', "%{$search}%")
						->orWhere('delivery_request_items.received_qty', 'like', "%{$search}%")
						->orWhere('delivery_request_items.defectived_qty', 'like', "%{$search}%")
						->orWhere('delivery_request_items.expired_at', 'like', "%{$search}%")
						->orWhere('delivery_request_items.remark', 'like', "%{$search}%")
						->orWhere('delivery_request_items.note', 'like', "%{$search}%");
				})
				->Where([
					['delivery_request_items.delivery_request_id', $delivery_request_id],
					['delivery_request_items.deleted_at', '=', null]
				])
				->count();
		}


		$data = array();

		if ($posts) {
			//loop posts collection to transfer in another array $nestedData
			foreach ($posts as $r) {
				$nestedData['reference_no'] = $r->reference_no;
				$nestedData['product_name'] = $r->product_name;
				$nestedData['qty'] = $r->qty;
				$nestedData['unit_measurement'] = $r->unit_measurement == null ? "-" : $r->note;
				$nestedData['received_qty'] = $r->received_qty;
				$nestedData['defectived_qty'] = $r->defectived_qty;
				$nestedData['expired_at'] = date('m-d-Y', strtotime($r->expired_at));
				$nestedData['remark'] = $r->remark ?? "-";
				$nestedData['note'] = $r->note == null ? "-" : $r->note;
				if ($r->status != "completed") {
					$nestedData['action'] = '
						<button 
							name="delete" 
							id="delete" 
							data-id="' . $r->id . '" 
							class="btn bg-gradient-danger btn-sm"
						>Delete</button>
						<button 
							name="edit" 
							id="edit" 
							data-productname="' . $r->product_name . '" 
							data-qty="' . $r->qty . '" 
							data-unit_measurement="' . $r->unit_measurement . '" 
							data-received_qty="' . $r->received_qty . '"
							data-defectived_qty="' . $r->defectived_qty . '"
							data-expired_at="' . date('m/d/Y', strtotime($r->expired_at)) . '"
							data-id="' . $r->id . '" 
							data-product-id="' . $r->product_id . '" 
							data-note="' . $r->note . '" 
							class="btn bg-gradient-warning btn-sm"
						>Edit</button>
					';
				} else {
					$nestedData['action'] = "-";
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

	//fetch Delivery Request Completed
	public function fetchDeliveriesRequest(Request $request)
	{
		//column list in the table Prpducts
		$columns = array(
			0 => 'reference_no',
			1 => 'suppliers.name',
			2 => 'status',
			3 => 'delivery_at',
			4 => 'action'
		);

		//get the total number of data in Product table
		$totalData = DeliveryRequest::where('status', 'completed')->count();
		//total number of data that will show in the datatable default 10
		$limit = $request->input('length');
		//start number for pagination ,default 0
		$start = $request->input('start');
		//order list of the column
		$order = $columns[$request->input('order.0.column')];
		//order by ,default asc 
		$dir = $request->input('order.0.dir');

		//check if user search for a value in the product datatable
		if (empty($request->input('search.value'))) {
			//get all the product data
			$posts = DeliveryRequest::select('*', 'delivery_requests.id as id')
				->join('suppliers', 'delivery_requests.supplier_id', '=', 'suppliers.id')
				->where('status', 'completed')
				->offset($start)
				->limit($limit)
				->orderBy('delivery_at', 'DESC')
				->get();

			//total number of filtered data
			$totalFiltered = DeliveryRequest::where('status', 'completed')->count();
		} else {
			$search = $request->input('search.value');

			$posts = DeliveryRequest::where('status', "completed")->where(function ($query) use ($search) {
				$query->whereHas('supplier', function ($query) use ($search) {
					$query->where('name', 'like', "%{$search}%");
				})
					->orWhere('reference_no', 'like', "%{$search}%")
					->orWhere('delivery_at', 'like', "%{$search}%");
			})
				->offset($start)
				->limit($limit)
				->orderBy('delivery_at', 'DESC')
				->get();

			//total number of filtered data matching the search value request in the delivery table	
			$totalFiltered = DeliveryRequest::where('status', "completed")->where(function ($query) use ($search) {
				$query->whereHas('supplier', function ($query) use ($search) {
					$query->where('name', 'like', "%{$search}%");
				})
					->orWhere('reference_no', 'like', "%{$search}%")
					->orWhere('delivery_at', 'like', "%{$search}%");
			})
				->count();
		}


		$data = array();

		if ($posts) {
			//loop posts collection to transfer in another array $nestedData
			foreach ($posts as $r) {
				// $status = '';
				// if ($r->status == 'cancel') {
				// 	$status = '<span title="Cancel" class="badge bg-danger">CANCEL</span>';
				// } else if ($r->status == 'pending') {
				// 	$status = '<span title="Danger" class="badge bg-warning">PENDING</span>';
				// } else if ($r->status == 'completed') {
				// 	$status = '<span title="Success" class="badge bg-success">COMPLETED</span>';
				// }
				$nestedData['reference_no'] = $r->reference_no;
				$nestedData['name'] = $r->supplier->name;
				$nestedData['status'] = '<span title="Success" class="badge bg-success">COMPLETED</span>';
				$nestedData['delivery_at'] = date('m-d-Y', strtotime($r->delivery_at));
				// if ($r->status == "completed" || $r->status == "cancel") {
				$nestedData['action'] = '
                    <button name="show" id="show" data-id="' . $r->id . '" class="btn bg-gradient-primary btn-sm">Show</button>
					<button name="edit"  disabled="disabled" class="btn bg-gradient-warning btn-sm">Edit</button>
					<button name="delete" disabled="disabled" class="btn bg-gradient-danger btn-sm">Delete</button>
					';
				// } else {
				// 	$nestedData['action'] = '
				//     <button name="show" id="show" data-id="' . $r->id . '" class="btn bg-gradient-primary btn-sm">Show</button>
				// 	<button name="edit" id="edit" data-id="' . $r->id . '" class="btn bg-gradient-warning btn-sm">Edit</button>
				// 	<button name="delete" id="delete" data-id="' . $r->id . '" class="btn bg-gradient-danger btn-sm">Delete</button>
				// ';
				// }

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

	public function fetchDeliveriesRequestPending(Request $request)
	{
		//column list in the table Prpducts
		$columns = array(
			0 => 'reference_no',
			1 => 'suppliers.name',
			2 => 'status',
			3 => 'delivery_at',
			4 => 'action'
		);

		//get the total number of data in Product table
		$totalData = DeliveryRequest::where('status', 'pending')->count();
		//total number of data that will show in the datatable default 10
		$limit = $request->input('length');
		//start number for pagination ,default 0
		$start = $request->input('start');
		//order list of the column
		$order = $columns[$request->input('order.0.column')];
		//order by ,default asc 
		$dir = $request->input('order.0.dir');

		//check if user search for a value in the product datatable
		if (empty($request->input('search.value'))) {
			//get all the product data
			$posts = DeliveryRequest::select('*', 'delivery_requests.id as id')
				->join('suppliers', 'delivery_requests.supplier_id', '=', 'suppliers.id')
				->where('status', 'pending')
				->offset($start)
				->limit($limit)
				->orderBy('delivery_at', 'DESC')
				->get();

			//total number of filtered data
			$totalFiltered = DeliveryRequest::where('status', 'pending')->count();
		} else {
			$search = $request->input('search.value');

			$posts = DeliveryRequest::where('status', 'pending')->where(function ($query) use ($search) {
				$query->whereHas('supplier', function ($query) use ($search) {
					$query->where('name', 'like', "%{$search}%");
				})
					->orWhere('reference_no', 'like', "%{$search}%")
					->orWhere('delivery_at', 'like', "%{$search}%");
			})
				->offset($start)
				->limit($limit)
				->orderBy('delivery_at', 'DESC')
				->get();

			//total number of filtered data matching the search value request in the delivery table	
			$totalFiltered = DeliveryRequest::where('status', 'pending')->where(function ($query) use ($search) {
				$query->whereHas('supplier', function ($query) use ($search) {
					$query->where('name', 'like', "%{$search}%");
				})
					->orWhere('reference_no', 'like', "%{$search}%")
					->orWhere('delivery_at', 'like', "%{$search}%");
			})
				->count();
		}


		$data = array();

		if ($posts) {
			//loop posts collection to transfer in another array $nestedData
			foreach ($posts as $r) {
				// $status = '';
				// if ($r->status == 'cancel') {
				// 	$status = '<span title="Cancel" class="badge bg-danger">CANCEL</span>';
				// } else if ($r->status == 'pending') {
				// 	$status = '<span title="Danger" class="badge bg-warning">PENDING</span>';
				// } else if ($r->status == 'completed') {
				// 	$status = '<span title="Success" class="badge bg-success">COMPLETED</span>';
				// }
				$nestedData['reference_no'] = $r->reference_no;
				$nestedData['name'] = $r->supplier->name;
				$nestedData['status'] = '<span title="Danger" class="badge bg-warning">PENDING</span>';
				$nestedData['delivery_at'] = date('m-d-Y', strtotime($r->delivery_at));
				// if ($r->status == "completed" || $r->status == "cancel") {
				// 	$nestedData['action'] = '
				//     <button name="show" id="show" data-id="' . $r->id . '" class="btn bg-gradient-primary btn-sm">Show</button>
				// 	<button name="edit"  disabled="disabled" class="btn bg-gradient-warning btn-sm">Edit</button>
				// 	<button name="delete" disabled="disabled" class="btn bg-gradient-danger btn-sm">Delete</button>
				// 	';
				// } else {
				$nestedData['action'] = '
                    <button name="show" id="show" data-id="' . $r->id . '" class="btn bg-gradient-primary btn-sm">Show</button>
					<button name="edit" id="edit" data-id="' . $r->id . '" class="btn bg-gradient-warning btn-sm">Edit</button>
					<button name="delete" id="delete" data-id="' . $r->id . '" class="btn bg-gradient-danger btn-sm">Delete</button>
				';
				//}

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

	public function fetchDeliveriesRequestCancel(Request $request)
	{
		//column list in the table Prpducts
		$columns = array(
			0 => 'reference_no',
			1 => 'suppliers.name',
			2 => 'status',
			3 => 'delivery_at',
			4 => 'action'
		);

		//get the total number of data in Product table
		$totalData = DeliveryRequest::where('status', 'cancel')->count();
		//total number of data that will show in the datatable default 10
		$limit = $request->input('length');
		//start number for pagination ,default 0
		$start = $request->input('start');
		//order list of the column
		$order = $columns[$request->input('order.0.column')];
		//order by ,default asc 
		$dir = $request->input('order.0.dir');

		//check if user search for a value in the product datatable
		if (empty($request->input('search.value'))) {
			//get all the product data
			$posts = DeliveryRequest::select('*', 'delivery_requests.id as id')
				->join('suppliers', 'delivery_requests.supplier_id', '=', 'suppliers.id')
				->where('status', 'cancel')
				->offset($start)
				->limit($limit)
				->orderBy('delivery_at', 'DESC')
				->get();

			//total number of filtered data
			$totalFiltered = DeliveryRequest::where('status', 'cancel')->count();
		} else {
			$search = $request->input('search.value');

			$posts = DeliveryRequest::where('status', 'cancel')->where(function ($query) use ($search) {
				$query->whereHas('supplier', function ($query) use ($search) {
					$query->where('name', 'like', "%{$search}%");
				})
					->orWhere('reference_no', 'like', "%{$search}%")
					->orWhere('delivery_at', 'like', "%{$search}%");
			})
				->offset($start)
				->limit($limit)
				->orderBy('delivery_at', 'DESC')
				->get();

			//total number of filtered data matching the search value request in the delivery table	
			$totalFiltered = DeliveryRequest::where('status', 'cancel')->where(function ($query) use ($search) {
				$query->whereHas('supplier', function ($query) use ($search) {
					$query->where('name', 'like', "%{$search}%");
				})
					->orWhere('reference_no', 'like', "%{$search}%")
					->orWhere('delivery_at', 'like', "%{$search}%");
			})
				->count();
		}


		$data = array();

		if ($posts) {
			//loop posts collection to transfer in another array $nestedData
			foreach ($posts as $r) {
				// $status = '';
				// if ($r->status == 'cancel') {
				// 	$status = '<span title="Cancel" class="badge bg-danger">CANCEL</span>';
				// } else if ($r->status == 'pending') {
				// 	$status = '<span title="Danger" class="badge bg-warning">PENDING</span>';
				// } else if ($r->status == 'completed') {
				// 	$status = '<span title="Success" class="badge bg-success">COMPLETED</span>';
				// }
				$nestedData['reference_no'] = $r->reference_no;
				$nestedData['name'] = $r->supplier->name;
				$nestedData['status'] = '<span title="Cancel" class="badge bg-danger">CANCEL</span>';
				$nestedData['delivery_at'] = date('m-d-Y', strtotime($r->delivery_at));
				// if ($r->status == "completed" || $r->status == "cancel") {
				$nestedData['action'] = '
                    <button name="show" id="show" data-id="' . $r->id . '" class="btn bg-gradient-primary btn-sm">Show</button>
					<button name="edit"  disabled="disabled" class="btn bg-gradient-warning btn-sm">Edit</button>
					<button name="delete" disabled="disabled" class="btn bg-gradient-danger btn-sm">Delete</button>
					';
				// } else {
				// 	$nestedData['action'] = '
				//     <button name="show" id="show" data-id="' . $r->id . '" class="btn bg-gradient-primary btn-sm">Show</button>
				// 	<button name="edit" id="edit" data-id="' . $r->id . '" class="btn bg-gradient-warning btn-sm">Edit</button>
				// 	<button name="delete" id="delete" data-id="' . $r->id . '" class="btn bg-gradient-danger btn-sm">Delete</button>
				// ';
				// }

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

	public function fetchInactiveDeliveriesRequest(Request $request)
	{
		//column list in the table Prpducts
		$columns = array(
			0 => 'reference_no',
			1 => 'suppliers.name',
			2 => 'status',
			3 => 'delivery_at',
			4 => 'action'
		);

		//get the total number of data in Product table
		$totalData = DeliveryRequest::onlyTrashed()->count();
		//total number of data that will show in the datatable default 10
		$limit = $request->input('length');
		//start number for pagination ,default 0
		$start = $request->input('start');
		//order list of the column
		$order = $columns[$request->input('order.0.column')];
		//order by ,default asc 
		$dir = $request->input('order.0.dir');

		//check if user search for a value in the product datatable
		if (empty($request->input('search.value'))) {
			//get all the product data
			$posts = DeliveryRequest::onlyTrashed()
				->select('*', 'delivery_requests.id as id')
				->join('suppliers', 'delivery_requests.supplier_id', '=', 'suppliers.id')
				->offset($start)
				->limit($limit)
				->orderBy('delivery_at', 'DESC')
				->get();

			//total number of filtered data
			$totalFiltered = DeliveryRequest::onlyTrashed()->count();
		} else {
			$search = $request->input('search.value');

			$posts = DeliveryRequest::onlyTrashed()
				->where(function ($query) use ($search) {
					$query->whereHas('supplier', function ($query) use ($search) {
						$query->where('name', 'like', "%{$search}%");
					})
						->orWhere('reference_no', 'like', "%{$search}%")
						->orWhere('status', 'like', "%{$search}%")
						->orWhere('delivery_at', 'like', "%{$search}%");
				})
				->offset($start)
				->limit($limit)
				->orderBy('delivery_at', 'DESC')
				->get();

			//total number of filtered data matching the search value request in the delivery table	
			$totalFiltered = DeliveryRequest::onlyTrashed()
				->where(function ($query) use ($search) {
					$query->whereHas('supplier', function ($query) use ($search) {
						$query->where('name', 'like', "%{$search}%");
					})
						->orWhere('reference_no', 'like', "%{$search}%")
						->orWhere('status', 'like', "%{$search}%")
						->orWhere('delivery_at', 'like', "%{$search}%");
				})
				->count();
		}


		$data = array();

		if ($posts) {
			//loop posts collection to transfer in another array $nestedData
			foreach ($posts as $r) {
				$status = '';
				if ($r->status == 'cancel') {
					$status = '<span title="Cancel" class="badge bg-danger">CANCEL</span>';
				} else if ($r->status == 'pending') {
					$status = '<span title="Danger" class="badge bg-warning">PENDING</span>';
				} else if ($r->status == 'completed') {
					$status = '<span title="Success" class="badge bg-success">COMPLETED</span>';
				}
				$nestedData['reference_no'] = $r->reference_no;
				$nestedData['name'] = $r->supplier->name;
				$nestedData['status'] = $status;
				$nestedData['delivery_at'] = date('m-d-Y', strtotime($r->delivery_at));
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

	//fetch Product Return Stock
	public function fetchProductsReturnStock(Request $request)
	{
		//column list in the table Prpducts
		$columns = array(
			0 => 'reference_no',
			1 => 'product_name',
			2 => 'qty',
			4 => 'remark',
			5 => 'note',
			6 => 'received_at',
			7 => 'delivery_at',
			8 => 'action'
		);

		$return_stock_id = $request->return_stock_id;

		//get the total number of data in Stock table
		$totalData = ReturnStockItem::where([
			['return_stock_id', $return_stock_id],
			['deleted_at', '=', null]
		])->count();
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
			//get all the Stock data
			$posts = DB::table('return_stock_items')
				->leftJoin('return_stocks', 'return_stock_items.return_stock_id', '=', 'return_stocks.id')
				->leftJoin('inventories', 'return_stock_items.product_id', '=', 'inventories.id')
				->select('return_stock_items.*', 'inventories.product_name', 'return_stocks.reference_no', 'return_stock_items.remark', 'return_stocks.delivery_at', 'return_stocks.received_at')
				->Where([
					['return_stock_items.return_stock_id', $return_stock_id],
					['return_stock_items.deleted_at', '=', null]
				])
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data
			$totalFiltered = DB::table('return_stock_items')
				->leftJoin('return_stocks', 'return_stock_items.return_stock_id', '=', 'return_stocks.id')
				->leftJoin('inventories', 'return_stock_items.product_id', '=', 'inventories.id')
				->select('return_stock_items.*', 'inventories.product_name', 'return_stock_items.remark', 'return_stocks.reference_no', 'return_stocks.delivery_at', 'return_stocks.received_at')
				->Where([
					['return_stock_items.return_stock_id', $return_stock_id],
					['return_stock_items.deleted_at', '=', null]
				])
				->count();
		} else {
			$search = $request->input('search.value');

			$posts =  DB::table('return_stock_items')
				->leftJoin('return_stocks', 'return_stock_items.return_stock_id', '=', 'return_stocks.id')
				->leftJoin('inventories', 'return_stock_items.product_id', '=', 'inventories.id')
				->select('return_stock_items.*', 'inventories.product_name', 'return_stock_items.remark', 'return_stocks.reference_no', 'return_stocks.delivery_at', 'return_stocks.received_at')
				->where(function ($query) use ($search) {
					$query->where('return_stocks.reference_no', 'like', '%' . $search . '%')
						->orWhere('inventories.product_name', 'like', "%{$search}%")
						->orWhere('return_stock_items.remark', 'like', "%{$search}%")
						->orWhere('return_stock_items.qty', 'like', "%{$search}%")
						->orWhere('return_stock_items.note', 'like', "%{$search}%")
						->orWhere('return_stocks.delivery_at', 'like', "%{$search}%")
						->orWhere('return_stocks.received_at', 'like', "%{$search}%");
				})
				->Where([
					['return_stock_items.return_stock_id', $return_stock_id],
					['return_stock_items.deleted_at', '=', null]
				])
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data matching the search value request in the Supplier table	
			$totalFiltered = DB::table('return_stock_items')
				->leftJoin('return_stocks', 'return_stock_items.return_stock_id', '=', 'return_stocks.id')
				->leftJoin('inventories', 'return_stock_items.product_id', '=', 'inventories.id')
				->select('return_stock_items.*', 'inventories.product_name', 'return_stock_items.remark', 'return_stocks.reference_no', 'return_stocks.delivery_at', 'return_stocks.received_at')
				->where(function ($query) use ($search) {
					$query->where('return_stocks.reference_no', 'like', '%' . $search . '%')
						->orWhere('inventories.product_name', 'like', "%{$search}%")
						->orWhere('return_stock_items.remark', 'like', "%{$search}%")
						->orWhere('return_stock_items.qty', 'like', "%{$search}%")
						->orWhere('return_stock_items.note', 'like', "%{$search}%")
						->orWhere('return_stocks.delivery_at', 'like', "%{$search}%")
						->orWhere('return_stocks.received_at', 'like', "%{$search}%");
				})
				->Where([
					['return_stock_items.return_stock_id', $return_stock_id],
					['return_stock_items.deleted_at', '=', null]
				])
				->count();
		}


		$data = array();

		if ($posts) {
			//loop posts collection to transfer in another array $nestedData
			foreach ($posts as $r) {
				$nestedData['reference_no'] = $r->reference_no;
				$nestedData['product_name'] = $r->product_name;
				$nestedData['qty'] = $r->qty;
				$nestedData['remark'] = ucwords($r->remark ?? "-");
				$nestedData['note'] = $r->note ?? "-";
				$nestedData['delivery_at'] = date('m-d-Y', strtotime($r->delivery_at));
				$nestedData['received_at'] = date('m-d-Y', strtotime($r->received_at));
				$nestedData['action'] = '
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

	//fetch Return Stock
	public function fetchReturnStock(Request $request)
	{
		//column list in the table Prpducts
		$columns = array(
			0 => 'reference_no',
			1 => 'suppliers.name',
			2 => 'delivery_at',
			3 => 'received_at',
			4 => 'action'
		);

		//get the total number of data in Product table
		$totalData = ReturnStock::count();
		//total number of data that will show in the datatable default 10
		$limit = $request->input('length');
		//start number for pagination ,default 0
		$start = $request->input('start');
		//order list of the column
		$order = $columns[$request->input('order.0.column')];
		//order by ,default asc 
		$dir = $request->input('order.0.dir');

		//check if user search for a value in the product datatable
		if (empty($request->input('search.value'))) {
			//get all the product data
			$posts = ReturnStock::select('*', 'return_stocks.id as id')
				->join('suppliers', 'return_stocks.supplier_id', '=', 'suppliers.id')
				->offset($start)
				->limit($limit)
				->orderBy('delivery_at', 'desc')
				->get();

			//total number of filtered data
			$totalFiltered = ReturnStock::count();
		} else {
			$search = $request->input('search.value');

			$posts = ReturnStock::where(function ($query) use ($search) {
				$query->whereHas('supplier', function ($query) use ($search) {
					$query->where('name', 'like', "%{$search}%");
				})
					->orWhere('reference_no', 'like', "%{$search}%")
					->orWhere('delivery_at', 'like', "%{$search}%")
					->orWhere('created_at', 'like', "%{$search}%");
			})
				->offset($start)
				->limit($limit)
				->orderBy('delivery_at', 'desc')
				->get();

			//total number of filtered data matching the search value request in the delivery table	
			$totalFiltered = ReturnStock::where(function ($query) use ($search) {
				$query->whereHas('supplier', function ($query) use ($search) {
					$query->where('name', 'like', "%{$search}%");
				})
					->orWhere('reference_no', 'like', "%{$search}%")
					->orWhere('delivery_at', 'like', "%{$search}%")
					->orWhere('created_at', 'like', "%{$search}%");
			})
				->count();
		}


		$data = array();

		if ($posts) {
			//loop posts collection to transfer in another array $nestedData
			foreach ($posts as $r) {
				$nestedData['reference_no'] = $r->reference_no;
				$nestedData['name'] = $r->supplier->name;
				$nestedData['delivery_at'] = date('m-d-Y', strtotime($r->delivery_at));
				$nestedData['received_at'] = date('m-d-Y', strtotime($r->received_at));
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

	public function fetchInactiveReturnStock(Request $request)
	{
		//column list in the table Prpducts
		$columns = array(
			0 => 'reference_no',
			1 => 'suppliers.name',
			2 => 'delivery_at',
			3 => 'received_at',
			4 => 'action'
		);

		//get the total number of data in Product table
		$totalData = ReturnStock::onlyTrashed()->count();
		//total number of data that will show in the datatable default 10
		$limit = $request->input('length');
		//start number for pagination ,default 0
		$start = $request->input('start');
		//order list of the column
		$order = $columns[$request->input('order.0.column')];
		//order by ,default asc 
		$dir = $request->input('order.0.dir');

		//check if user search for a value in the product datatable
		if (empty($request->input('search.value'))) {
			//get all the product data
			$posts = ReturnStock::onlyTrashed()
				->select('*', 'return_stocks.id as id')
				->join('suppliers', 'return_stocks.supplier_id', '=', 'suppliers.id')
				->offset($start)
				->limit($limit)
				->orderBy('delivery_at', 'desc')
				->get();

			//total number of filtered data
			$totalFiltered = ReturnStock::onlyTrashed()->count();
		} else {
			$search = $request->input('search.value');

			$posts = ReturnStock::onlyTrashed()
				->where(function ($query) use ($search) {
					$query->whereHas('supplier', function ($query) use ($search) {
						$query->where('name', 'like', "%{$search}%");
					})
						->orWhere('reference_no', 'like', "%{$search}%")
						->orWhere('delivery_at', 'like', "%{$search}%")
						->orWhere('created_at', 'like', "%{$search}%");
				})
				->offset($start)
				->limit($limit)
				->orderBy('delivery_at', 'desc')
				->get();

			//total number of filtered data matching the search value request in the delivery table	
			$totalFiltered = ReturnStock::onlyTrashed()
				->where(function ($query) use ($search) {
					$query->whereHas('supplier', function ($query) use ($search) {
						$query->where('name', 'like', "%{$search}%");
					})
						->orWhere('reference_no', 'like', "%{$search}%")
						->orWhere('delivery_at', 'like', "%{$search}%")
						->orWhere('created_at', 'like', "%{$search}%");
				})
				->count();
		}


		$data = array();

		if ($posts) {
			//loop posts collection to transfer in another array $nestedData
			foreach ($posts as $r) {
				$nestedData['reference_no'] = $r->reference_no;
				$nestedData['name'] = $r->supplier->name;
				$nestedData['delivery_at'] = date('m-d-Y', strtotime($r->delivery_at));
				$nestedData['received_at'] = date('m-d-Y', strtotime($r->received_at));
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
