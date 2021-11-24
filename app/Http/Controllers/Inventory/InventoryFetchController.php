<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Inventory;
use App\InventoryLevel;

class InventoryFetchController extends Controller
{
    public function fetchInventory(Request $request)
    {
		//column list in the table Prpducts
        $columns = array(
			0 => 'product_name',
			1 => 'generic_name',
			2 => 'content',
			3 => 'category_name',
            4 => 'original_price',
            5 => 'selling_price',
            6 => 'quantity',
			7 => 'created_at',
			8 => 'action'
		);
		
		//get the total number of data in Inventory table
		$totalData = Inventory::count();
		//total number of data that will show in the datatable default 10
		$limit = $request->input('length');
		//start number for pagination ,default 0
		$start = $request->input('start');
		//order list of the column
		$order = $columns[$request->input('order.0.column')];
		//order by ,default asc 
		$dir = $request->input('order.0.dir');
		
		//check if user search for a value in the inventory datatable
		if(empty($request->input('search.value'))){
			//get all the inventory data
            $posts = DB::table('inventories')
                        ->leftJoin('products', 'inventories.product_id', '=', 'products.id')
                        ->leftJoin('category_per_products', 'category_per_products.product_id', '=', 'products.id')
                        ->leftJoin('categories', 'category_per_products.category_id', '=', 'categories.id')
                        ->select('inventories.*', 'products.product_name','products.generic_name',
                        'products.content','categories.category_name')
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order,$dir)
                        ->get();
			
			//total number of filtered data
			$totalFiltered = DB::table('inventories')
                                ->leftJoin('products', 'inventories.product_id', '=', 'products.id')
                                ->leftJoin('category_per_products', 'category_per_products.product_id', '=', 'products.id')
                                ->leftJoin('categories', 'category_per_products.category_id', '=', 'categories.id')
                                ->select('inventories.*', 'products.product_name','products.generic_name',
                                'products.content','categories.category_name')
                                ->count();
		}else{
            $search = $request->input('search.value');
            
			$posts = DB::table('inventories')
                        ->leftJoin('products', 'inventories.product_id', '=', 'products.id')
                        ->leftJoin('category_per_products', 'category_per_products.product_id', '=', 'products.id')
                        ->leftJoin('categories', 'category_per_products.category_id', '=', 'categories.id')
                        ->select('inventories.*', 'products.product_name','products.generic_name',
                        'products.content','categories.category_name')
                        ->orWhere('inventories.original_price','like',"%{$search}%")
                        ->orWhere('inventories.selling_price','like',"%{$search}%")
                        ->orWhere('inventories.quantity','like',"%{$search}%")
                        ->orWhere('inventories.created_at','like',"%{$search}%")
                        ->orWhere('products.product_name','like',"%{$search}%")
                        ->orWhere('products.generic_name','like',"%{$search}%")
                        ->orWhere('products.content','like',"%{$search}%")
                        ->orWhere('categories.category_name','like',"%{$search}%")
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();

			//total number of filtered data matching the search value request in the product table	
            $totalFiltered = DB::table('inventories')
                            ->leftJoin('products', 'inventories.product_id', '=', 'products.id')
                            ->leftJoin('category_per_products', 'category_per_products.product_id', '=', 'products.id')
                            ->leftJoin('categories', 'category_per_products.category_id', '=', 'categories.id')
                            ->select('inventories.*', 'products.product_name','products.generic_name',
                            'products.content','categories.category_name')
                            ->orWhere('inventories.original_price','like',"%{$search}%")
                            ->orWhere('inventories.selling_price','like',"%{$search}%")
                            ->orWhere('inventories.quantity','like',"%{$search}%")
                            ->orWhere('inventories.created_at','like',"%{$search}%")
                            ->orWhere('products.product_name','like',"%{$search}%")
                            ->orWhere('products.generic_name','like',"%{$search}%")
                            ->orWhere('products.content','like',"%{$search}%")
                            ->orWhere('categories.category_name','like',"%{$search}%")
							->count();
		}		
					
		
		$data = array();
		
		if($posts){
            $inventoryLevel = InventoryLevel::all();
			//loop posts collection to transfer in another array $nestedData
			foreach($posts as $r){
                $status = '';
                if($r->quantity == 0){
                    $status = '<span title="Danger" class="badge bg-danger">Danger</span>';
                }else if($r->quantity <= $inventoryLevel[0]->re_stock){
                    $status = '<span title="Danger" class="badge bg-danger">Re-Stock</span>';
                }else if($r->quantity == $inventoryLevel[0]->critical){
                    $status = '<span title="Danger" class="badge bg-warning">Critical</span>';
                }else if($r->quantity > $inventoryLevel[0]->critical){
                    $status = '<span title="Danger" class="badge bg-success">Average</span>';
                }
				$nestedData['product_name'] = $r->product_name;
				$nestedData['generic_name'] = $r->generic_name;
				$nestedData['content'] = $r->content;
				$nestedData['category_name'] = $r->category_name;
                $nestedData['original_price'] = $r->original_price;
                $nestedData['selling_price'] = $r->selling_price;
                $nestedData['quantity'] = $r->quantity;
				$nestedData['created_at'] = date('d-m-Y',strtotime($r->created_at));
                $nestedData['status'] = $status;
                $nestedData['action'] = '
					<button name="edit" id="edit" data-id="'.$r->id.'" class="btn bg-gradient-warning btn-sm">Edit</button>
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

    public function fetchInventoryProducts(){
        try {
            $inventories = DB::table('inventories')
                                ->leftJoin('products', 'inventories.product_id', '=', 'products.id')
                                ->leftJoin('category_per_products', 'category_per_products.product_id', '=', 'products.id')
                                ->leftJoin('categories', 'category_per_products.category_id', '=', 'categories.id')
                                ->select('inventories.*', 'products.product_name','products.sku', 'products.generic_name',
                                'products.content','categories.category_name')
                                ->where('inventories.quantity','>',0)
                                ->get();

            return response()->json([
                    'inventories' => $inventories,
                    'message' => 'Inventories Data Fetch Successful'
                ], 200);
            
        } catch(\Exception $e) {
            return response()->json([
                'data' => $e->getMessage(),
                'message' => 'Inventories Data Fetch Failed'
            ], 500); 
        }
    }
}