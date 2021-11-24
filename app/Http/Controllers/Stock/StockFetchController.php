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
		if(empty($request->input('search.value'))){
			//get all the product data
			$posts = Delivery::offset($start)
					->limit($limit)
					->orderBy($order,$dir)
					->get();
			
			//total number of filtered data
			$totalFiltered = Delivery::count();
		}else{
            $search = $request->input('search.value');
            
			$posts = Delivery::where('reference_no', 'like', "%{$search}%")
							->orWhere('received_by','like',"%{$search}%")
							->orWhere('received_at','like',"%{$search}%")
							->offset($start)
							->limit($limit)
							->orderBy($order, $dir)
							->get();

			//total number of filtered data matching the search value request in the delivery table	
            $totalFiltered = Delivery::where('reference_no', 'like', "%{$search}%")
                            ->orWhere('received_by','like',"%{$search}%")
                            ->orWhere('received_at','like',"%{$search}%")
							->count();
		}		
					
		
		$data = array();
		
		if($posts){
			//loop posts collection to transfer in another array $nestedData
			foreach($posts as $r){
				$nestedData['reference_no'] = $r->reference_no;
				$nestedData['received_by'] = $r->received_by;
				$nestedData['received_at'] = date('d-m-Y',strtotime($r->received_at));
                $nestedData['action'] = '
                    <button name="show" id="show" data-id="'.$r->id.'" class="btn bg-gradient-primary btn-sm">Show</button>
					<button name="edit" id="edit" data-id="'.$r->id.'" class="btn bg-gradient-warning btn-sm">Edit</button>
					<button name="delete" id="delete" data-id="'.$r->id.'" class="btn bg-gradient-danger btn-sm">Delete</button>
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
			1 => 'received_by',
			2 => 'product_name',
			3 => 'qty',
			4 => 'expired_at',
			5 => 'received_at',
			6 => 'action'
		);

		$delivery_id = $request->delivery_id;
		
		//get the total number of data in Stock table
		$totalData = Stock::where([
			['delivery_id',$delivery_id],
			['deleted_at','=',null]
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
		if(empty($request->input('search.value'))){
			//get all the Stock data
			$posts = DB::table('stocks')
						->leftJoin('deliveries', 'stocks.delivery_id', '=', 'deliveries.id')
						->leftJoin('products', 'stocks.product_id', '=', 'products.id')
						->select('stocks.*', 'products.product_name','deliveries.reference_no','deliveries.received_by')
						->where([
							['stocks.delivery_id',$delivery_id],
							['stocks.deleted_at','=',null]
						])
						->offset($start)
						->limit($limit)
						->orderBy($order,$dir)
						->get();
			
			//total number of filtered data
			$totalFiltered = DB::table('stocks')
								->leftJoin('deliveries', 'stocks.delivery_id', '=', 'deliveries.id')
								->leftJoin('products', 'stocks.product_id', '=', 'products.id')
								->select('stocks.*', 'products.product_name','deliveries.reference_no','deliveries.received_by')
								->where([
									['stocks.delivery_id',$delivery_id],
									['stocks.deleted_at','=',null]
								])
								->count();
		}else{
            $search = $request->input('search.value');
            
			$posts = DB::table('stocks')
							->leftJoin('deliveries', 'stocks.delivery_id', '=', 'deliveries.id')
							->leftJoin('products', 'stocks.product_id', '=', 'products.id')
							->select('stocks.*', 'products.product_name','deliveries.reference_no','deliveries.received_by')
							->orWhere('deliveries.reference_no','like',"%{$search}%")
							->orWhere('deliveries.received_by','like',"%{$search}%")
							->orWhere('products.product_name','like',"%{$search}%")
							->orWhere('stocks.qty','like',"%{$search}%")
							->orWhere('stocks.expired_at','like',"%{$search}%")
							->orWhere('deliveries.received_at','like',"%{$search}%")
							->where([
								['stocks.delivery_id',$delivery_id],
								['stocks.deleted_at','=',null]
							])
							->offset($start)
							->limit($limit)
							->orderBy($order, $dir)
							->get();

			//total number of filtered data matching the search value request in the Supplier table	
            $totalFiltered = DB::table('stocks')
								->leftJoin('deliveries', 'stocks.delivery_id', '=', 'deliveries.id')
								->leftJoin('products', 'stocks.product_id', '=', 'products.id')
								->select('stocks.*', 'products.product_name','deliveries.reference_no','deliveries.received_by')
								->orWhere('deliveries.reference_no','like',"%{$search}%")
								->orWhere('deliveries.received_by','like',"%{$search}%")
								->orWhere('products.product_name','like',"%{$search}%")
								->orWhere('stocks.qty','like',"%{$search}%")
								->orWhere('stocks.expired_at','like',"%{$search}%")
								->orWhere('deliveries.received_at','like',"%{$search}%")
								->where([
									['stocks.delivery_id',$delivery_id],
									['stocks.deleted_at','=',null]
								])
								->count();
		}		
					
		
		$data = array();
		
		if($posts){
			//loop posts collection to transfer in another array $nestedData
			foreach($posts as $r){
				$nestedData['reference_no'] = $r->reference_no;
				$nestedData['received_by'] = $r->received_by;
				$nestedData['product_name'] = $r->product_name;
				$nestedData['qty'] = $r->qty;
				$nestedData['expired_at'] = date('m-d-Y',strtotime($r->expired_at));
				$nestedData['received_at'] = date('m-d-Y',strtotime($r->created_at));
                $nestedData['action'] = '
					<button name="delete" id="delete" data-id="'.$r->id.'" class="btn bg-gradient-danger btn-sm">Delete</button>
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
			1 => 'received_by',
			2 => 'product_name',
			3 => 'qty',
			4 => 'expired_at',
			5 => 'received_at'
		);
		
		//get the total number of data in Stock table
		$totalData = Stock::count();
		//total number of data that will show in the datatable default 10
		$limit = $request->input('length');
		//start number for pagination ,default 0
		$start = $request->input('start');
		//order list of the column
		$order = $columns[$request->input('order.0.column')];
		//order by ,default asc 
		$dir = $request->input('order.0.dir');
		
		//check if user search for a value in the Supplier datatable
		if(empty($request->input('search.value'))){
			//get all the Stock data
			$posts = DB::table('stocks')
						->leftJoin('deliveries', 'stocks.delivery_id', '=', 'deliveries.id')
						->leftJoin('products', 'stocks.product_id', '=', 'products.id')
						->select('stocks.*', 'products.product_name','deliveries.reference_no','deliveries.received_by')
						->offset($start)
						->limit($limit)
						->orderBy($order,$dir)
						->get();
			
			//total number of filtered data
			$totalFiltered = DB::table('stocks')
								->leftJoin('deliveries', 'stocks.delivery_id', '=', 'deliveries.id')
								->leftJoin('products', 'stocks.product_id', '=', 'products.id')
								->select('stocks.*', 'products.product_name','deliveries.reference_no','deliveries.received_by')
								->count();
		}else{
            $search = $request->input('search.value');
            
			$posts = DB::table('stocks')
							->leftJoin('deliveries', 'stocks.delivery_id', '=', 'deliveries.id')
							->leftJoin('products', 'stocks.product_id', '=', 'products.id')
							->select('stocks.*', 'products.product_name','deliveries.reference_no','deliveries.received_by')
							->orWhere('deliveries.reference_no','like',"%{$search}%")
							->orWhere('deliveries.received_by','like',"%{$search}%")
							->orWhere('products.product_name','like',"%{$search}%")
							->orWhere('stocks.qty','like',"%{$search}%")
							->orWhere('stocks.expired_at','like',"%{$search}%")
							->orWhere('deliveries.received_at','like',"%{$search}%")
							->offset($start)
							->limit($limit)
							->orderBy($order, $dir)
							->get();

			//total number of filtered data matching the search value request in the Supplier table	
            $totalFiltered = DB::table('stocks')
								->leftJoin('deliveries', 'stocks.delivery_id', '=', 'deliveries.id')
								->leftJoin('products', 'stocks.product_id', '=', 'products.id')
								->select('stocks.*', 'products.product_name','deliveries.reference_no','deliveries.received_by')
								->orWhere('deliveries.reference_no','like',"%{$search}%")
								->orWhere('deliveries.received_by','like',"%{$search}%")
								->orWhere('products.product_name','like',"%{$search}%")
								->orWhere('stocks.qty','like',"%{$search}%")
								->orWhere('stocks.expired_at','like',"%{$search}%")
								->orWhere('deliveries.received_at','like',"%{$search}%")
								->count();
		}		
					
		
		$data = array();
		
		if($posts){
			//loop posts collection to transfer in another array $nestedData
			foreach($posts as $r){
				$nestedData['reference_no'] = $r->reference_no;
				$nestedData['received_by'] = $r->received_by;
				$nestedData['product_name'] = $r->product_name;
				$nestedData['qty'] = $r->qty;
				$nestedData['expired_at'] = date('d-m-Y',strtotime($r->expired_at));
				$nestedData['received_at'] = date('d-m-Y',strtotime($r->created_at));
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
			3 => 'delivery_at',
			4 => 'action'
		);

		$delivery_request_id = $request->delivery_request_id;
		
		//get the total number of data in Stock table
		$totalData = DeliveryRequestItem::where([
			['delivery_request_id',$delivery_request_id],
			['deleted_at','=',null]
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
		if(empty($request->input('search.value'))){
			//get all the Stock data
			$posts = DB::table('delivery_request_items')
						->leftJoin('delivery_requests', 'delivery_request_items.delivery_request_id', '=', 'delivery_requests.id')
						->leftJoin('products', 'delivery_request_items.product_id', '=', 'products.id')
						->select('delivery_request_items.*', 'products.product_name','delivery_requests.reference_no','delivery_requests.delivery_at')
						->where([
							['delivery_request_items.delivery_request_id',$delivery_request_id],
							['delivery_request_items.deleted_at','=',null]
						])
						->offset($start)
						->limit($limit)
						->orderBy($order,$dir)
						->get();
			
			//total number of filtered data
			$totalFiltered = DB::table('delivery_request_items')
								->leftJoin('delivery_requests', 'delivery_request_items.delivery_request_id', '=', 'delivery_requests.id')
								->leftJoin('products', 'delivery_request_items.product_id', '=', 'products.id')
								->select('delivery_request_items.*', 'products.product_name','delivery_requests.reference_no','delivery_requests.delivery_at')
								->where([
									['delivery_request_items.delivery_request_id',$delivery_request_id],
									['delivery_request_items.deleted_at','=',null]
								])
								->count();
		}else{
            $search = $request->input('search.value');
            
			$posts = DB::table('delivery_request_items')
							->leftJoin('delivery_requests', 'delivery_request_items.delivery_request_id', '=', 'delivery_requests.id')
							->leftJoin('products', 'delivery_request_items.product_id', '=', 'products.id')
							->select('delivery_request_items.*', 'products.product_name','delivery_requests.reference_no','delivery_requests.delivery_at')
							->orWhere('delivery_requests.reference_no','like',"%{$search}%")
							->orWhere('products.product_name','like',"%{$search}%")
							->orWhere('delivery_request_items.qty','like',"%{$search}%")
							->orWhere('delivery_requests.delivery_at','like',"%{$search}%")
							->where([
								['delivery_request_items.delivery_request_id',$delivery_request_id],
								['delivery_request_items.deleted_at','=',null]
							])
							->offset($start)
							->limit($limit)
							->orderBy($order, $dir)
							->get();

			//total number of filtered data matching the search value request in the Supplier table	
            $totalFiltered = DB::table('delivery_request_items')
								->leftJoin('delivery_requests', 'delivery_request_items.delivery_request_id', '=', 'delivery_requests.id')
								->leftJoin('products', 'delivery_request_items.product_id', '=', 'products.id')
								->select('delivery_request_items.*', 'products.product_name','delivery_requests.reference_no','delivery_requests.delivery_at')
								->orWhere('delivery_requests.reference_no','like',"%{$search}%")
								->orWhere('products.product_name','like',"%{$search}%")
								->orWhere('delivery_request_items.qty','like',"%{$search}%")
								->orWhere('delivery_requests.delivery_at','like',"%{$search}%")
								->where([
									['delivery_request_items.delivery_request_id',$delivery_request_id],
									['delivery_request_items.deleted_at','=',null]
								])
								->count();
		}		
					
		
		$data = array();
		
		if($posts){
			//loop posts collection to transfer in another array $nestedData
			foreach($posts as $r){
				$nestedData['reference_no'] = $r->reference_no;
				$nestedData['product_name'] = $r->product_name;
				$nestedData['qty'] = $r->qty;
				$nestedData['delivery_at'] = date('m-d-Y',strtotime($r->delivery_at));
                $nestedData['action'] = '
					<button name="delete" id="delete" data-id="'.$r->id.'" class="btn bg-gradient-danger btn-sm">Delete</button>
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

	//fetch Delivery Request
	public function fetchDeliveriesRequest(Request $request)
	{
		//column list in the table Prpducts
        $columns = array(
			0 => 'reference_no',
			1 => 'name',
			2 => 'content',
			3 => 'delivery_at',
			4 => 'action'
		);
		
		//get the total number of data in Product table
		$totalData = DeliveryRequest::count();
		//total number of data that will show in the datatable default 10
		$limit = $request->input('length');
		//start number for pagination ,default 0
		$start = $request->input('start');
		//order list of the column
		$order = $columns[$request->input('order.0.column')];
		//order by ,default asc 
		$dir = $request->input('order.0.dir');
		
		//check if user search for a value in the product datatable
		if(empty($request->input('search.value'))){
			//get all the product data
			$posts = DeliveryRequest::with('supplier')->offset($start)
					->limit($limit)
					->orderBy($order,$dir)
					->get();
			
			//total number of filtered data
			$totalFiltered = DeliveryRequest::count();
		}else{
            $search = $request->input('search.value');
            
			$posts = DeliveryRequest::where('reference_no', 'like', "%{$search}%")
							->orWhereHas('supplier', function ($query) use ($search) {
								$query->where('name', 'like', "%{$search}%");
							})
							->orWhere('notes','like',"%{$search}%")
							->orWhere('delivery_at','like',"%{$search}%")
							->offset($start)
							->limit($limit)
							->orderBy($order, $dir)
							->get();

			//total number of filtered data matching the search value request in the delivery table	
            $totalFiltered = Delivery::where('reference_no', 'like', "%{$search}%")
								->orWhereHas('supplier', function ($query) use ($search) {
									$query->where('name', 'like', "%{$search}%");
								})
								->orWhere('notes','like',"%{$search}%")
								->orWhere('delivery_at','like',"%{$search}%")
								->count();
		}		
					
		
		$data = array();
		
		if($posts){
			//loop posts collection to transfer in another array $nestedData
			foreach($posts as $r){
				$nestedData['reference_no'] = $r->reference_no;
				$nestedData['content'] = $r->content;
				$nestedData['name'] = $r->supplier->name;
				$nestedData['delivery_at'] = date('m-d-Y',strtotime($r->delivery_at));
                $nestedData['action'] = '
                    <button name="show" id="show" data-id="'.$r->id.'" class="btn bg-gradient-primary btn-sm">Show</button>
					<button name="edit" id="edit" data-id="'.$r->id.'" class="btn bg-gradient-warning btn-sm">Edit</button>
					<button name="delete" id="delete" data-id="'.$r->id.'" class="btn bg-gradient-danger btn-sm">Delete</button>
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
			4 => 'received_at',
			5 => 'delivery_at',
			6 => 'action'
		);

		$return_stock_id = $request->return_stock_id;
		
		//get the total number of data in Stock table
		$totalData = ReturnStockItem::where([
			['return_stock_id',$return_stock_id],
			['deleted_at','=',null]
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
		if(empty($request->input('search.value'))){
			//get all the Stock data
			$posts = DB::table('return_stock_items')
						->leftJoin('return_stocks', 'return_stock_items.return_stock_id', '=', 'return_stocks.id')
						->leftJoin('products', 'return_stock_items.product_id', '=', 'products.id')
						->select('return_stock_items.*', 'products.product_name','return_stocks.reference_no','return_stocks.delivery_at','return_stocks.received_at')
						->where([
							['return_stock_items.return_stock_id',$return_stock_id],
							['return_stock_items.deleted_at','=',null]
						])
						->offset($start)
						->limit($limit)
						->orderBy($order,$dir)
						->get();
			
			//total number of filtered data
			$totalFiltered = DB::table('return_stock_items')
								->leftJoin('return_stocks', 'return_stock_items.return_stock_id', '=', 'return_stocks.id')
								->leftJoin('products', 'return_stock_items.product_id', '=', 'products.id')
								->select('return_stock_items.*', 'products.product_name','return_stocks.reference_no','return_stocks.delivery_at','return_stocks.received_at')
								->where([
									['return_stock_items.return_stock_id',$return_stock_id],
									['return_stock_items.deleted_at','=',null]
								])
								->count();
		}else{
            $search = $request->input('search.value');
            
			$posts =  DB::table('return_stock_items')
							->leftJoin('return_stocks', 'return_stock_items.return_stock_id', '=', 'return_stocks.id')
							->leftJoin('products', 'return_stock_items.product_id', '=', 'products.id')
							->select('return_stock_items.*', 'products.product_name','return_stocks.reference_no','return_stocks.delivery_at','return_stocks.received_at')
							->orWhere('return_stocks.reference_no','like',"%{$search}%")
							->orWhere('products.product_name','like',"%{$search}%")
							->orWhere('return_stock_items.qty','like',"%{$search}%")
							->orWhere('return_stocks.delivery_at','like',"%{$search}%")
							->orWhere('return_stocks.received_at','like',"%{$search}%")
							->where([
								['return_stock_items.return_stock_id',$return_stock_id],
								['return_stock_items.deleted_at','=',null]
							])
							->offset($start)
							->limit($limit)
							->orderBy($order, $dir)
							->get();

			//total number of filtered data matching the search value request in the Supplier table	
            $totalFiltered = DB::table('return_stock_items')
								->leftJoin('return_stocks', 'return_stock_items.return_stock_id', '=', 'return_stocks.id')
								->leftJoin('products', 'return_stock_items.product_id', '=', 'products.id')
								->select('return_stock_items.*', 'products.product_name','return_stocks.reference_no','return_stocks.delivery_at','return_stocks.received_at')
								->orWhere('return_stocks.reference_no','like',"%{$search}%")
								->orWhere('products.product_name','like',"%{$search}%")
								->orWhere('return_stock_items.qty','like',"%{$search}%")
								->orWhere('return_stocks.delivery_at','like',"%{$search}%")
								->orWhere('return_stocks.received_at','like',"%{$search}%")
								->where([
									['return_stock_items.return_stock_id',$return_stock_id],
									['return_stock_items.deleted_at','=',null]
								])
								->count();
		}		
					
		
		$data = array();
		
		if($posts){
			//loop posts collection to transfer in another array $nestedData
			foreach($posts as $r){
				$nestedData['reference_no'] = $r->reference_no;
				$nestedData['product_name'] = $r->product_name;
				$nestedData['qty'] = $r->qty;
				$nestedData['delivery_at'] = date('m-d-Y',strtotime($r->delivery_at));
				$nestedData['received_at'] = date('m-d-Y',strtotime($r->received_at));
                $nestedData['action'] = '
					<button name="delete" id="delete" data-id="'.$r->id.'" class="btn bg-gradient-danger btn-sm">Delete</button>
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

	//fetch Delivery Request
	public function fetchReturnStock(Request $request)
	{
		//column list in the table Prpducts
        $columns = array(
			0 => 'reference_no',
			1 => 'name',
			2 => 'content',
			3 => 'delivery_at',
			4 => 'received_at',
			5 => 'action'
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
		if(empty($request->input('search.value'))){
			//get all the product data
			$posts = ReturnStock::with('supplier')->offset($start)
					->limit($limit)
					->orderBy($order,$dir)
					->get();
			
			//total number of filtered data
			$totalFiltered = ReturnStock::count();
		}else{
            $search = $request->input('search.value');
            
			$posts = ReturnStock::where('reference_no', 'like', "%{$search}%")
							->orWhereHas('supplier', function ($query) use ($search) {
								$query->where('name', 'like', "%{$search}%");
							})
							->orWhere('notes','like',"%{$search}%")
							->orWhere('delivery_at','like',"%{$search}%")
							->orWhere('created_at','like',"%{$search}%")
							->offset($start)
							->limit($limit)
							->orderBy($order, $dir)
							->get();

			//total number of filtered data matching the search value request in the delivery table	
            $totalFiltered = ReturnStock::where('reference_no', 'like', "%{$search}%")
								->orWhereHas('supplier', function ($query) use ($search) {
									$query->where('name', 'like', "%{$search}%");
								})
								->orWhere('notes','like',"%{$search}%")
								->orWhere('delivery_at','like',"%{$search}%")
								->orWhere('received_at','like',"%{$search}%")
								->count();
		}		
					
		
		$data = array();
		
		if($posts){
			//loop posts collection to transfer in another array $nestedData
			foreach($posts as $r){
				$nestedData['reference_no'] = $r->reference_no;
				$nestedData['content'] = $r->content;
				$nestedData['name'] = $r->supplier->name;
				$nestedData['delivery_at'] = date('m-d-Y',strtotime($r->delivery_at));
				$nestedData['received_at'] = date('m-d-Y',strtotime($r->received_at));
                $nestedData['action'] = '
                    <button name="show" id="show" data-id="'.$r->id.'" class="btn bg-gradient-primary btn-sm">Show</button>
					<button name="edit" id="edit" data-id="'.$r->id.'" class="btn bg-gradient-warning btn-sm">Edit</button>
					<button name="delete" id="delete" data-id="'.$r->id.'" class="btn bg-gradient-danger btn-sm">Delete</button>
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