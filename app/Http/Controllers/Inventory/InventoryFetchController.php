<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Inventory;
use App\InventoryAdjustment;
use App\InventoryLevel;
use App\SaleItem;

class InventoryFetchController extends Controller
{
    public function fetchInventory(Request $request)
    {
        //column list in the table Prpducts
        $columns = array(
            0 => 'product_name',
            1 => 'generic_name',
            2 => 'sku',
            3 => 'content',
            4 => 'category_name',
            5 => 'original_price',
            6 => 'selling_price',
            7 => 'quantity',
            8 => 'quantity',
            9 => 'created_at',
            10 => 'action'
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
        if (empty($request->input('search.value'))) {
            //search dropdown
            $productName = $request->input('columns.0.search.value');
            $genericName = $request->input('columns.1.search.value');
            $sku = $request->input('columns.2.search.value');
            $categoryName = $request->input('columns.3.search.value');
            $details = $request->input('columns.4.search.value');
            $originalPrice = $request->input('columns.5.search.value');
            $sellingPrice = $request->input('columns.6.search.value');
            $stock = $request->input('columns.7.search.value');
            $status = $request->input('columns.8.search.value');
            $dateAdded = $request->input('columns.9.search.value');
            //get all the inventory data
            $posts = DB::table('inventories')
                ->leftJoin('category_per_products', 'category_per_products.product_id', '=', 'inventories.id')
                ->leftJoin('categories', 'category_per_products.category_id', '=', 'categories.id')
                ->select('inventories.*', 'categories.category_name')
                ->where('inventories.product_name', 'like', "%{$productName}%")
                ->where('inventories.generic_name', 'like', "%{$genericName}%")
                ->where('inventories.sku', 'like', "%{$sku}%")
                ->where('categories.category_name', 'like', "%{$categoryName}%")
                ->where('inventories.content', 'like', "%{$details}%")
                ->where('inventories.original_price', 'like', "%{$originalPrice}%")
                ->where('inventories.selling_price', 'like', "%{$sellingPrice}%")
                ->where('inventories.quantity', 'like', "%{$stock}%")
                ->where('inventories.created_at', 'like', "%{$dateAdded}%")
                ->where('inventories.deleted_at', '=', null)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

            //total number of filtered data
            $totalFiltered = DB::table('inventories')
                ->leftJoin('category_per_products', 'category_per_products.product_id', '=', 'inventories.id')
                ->leftJoin('categories', 'category_per_products.category_id', '=', 'categories.id')
                ->select('inventories.*', 'categories.category_name')
                ->where('inventories.product_name', 'like', "%{$productName}%")
                ->where('inventories.generic_name', 'like', "%{$genericName}%")
                ->where('inventories.sku', 'like', "%{$sku}%")
                ->where('categories.category_name', 'like', "%{$categoryName}%")
                ->where('inventories.content', 'like', "%{$details}%")
                ->where('inventories.original_price', 'like', "%{$originalPrice}%")
                ->where('inventories.selling_price', 'like', "%{$sellingPrice}%")
                ->where('inventories.quantity', 'like', "%{$stock}%")
                ->where('inventories.created_at', 'like', "%{$dateAdded}%")
                ->where('inventories.deleted_at', '=', null)
                ->count();
        } else {
            $search = $request->input('search.value');

            $posts = DB::table('inventories')
                ->leftJoin('category_per_products', 'category_per_products.product_id', '=', 'inventories.id')
                ->leftJoin('categories', 'category_per_products.category_id', '=', 'categories.id')
                ->select('inventories.*', 'categories.category_name')
                ->orWhere('inventories.original_price', 'like', "%{$search}%")
                ->orWhere('inventories.selling_price', 'like', "%{$search}%")
                ->orWhere('inventories.quantity', 'like', "%{$search}%")
                ->orWhere('inventories.created_at', 'like', "%{$search}%")
                ->orWhere('inventories.product_name', 'like', "%{$search}%")
                ->orWhere('inventories.generic_name', 'like', "%{$search}%")
                ->orWhere('inventories.sku', 'like', "%{$search}%")
                ->orWhere('inventories.content', 'like', "%{$search}%")
                ->orWhere('categories.category_name', 'like', "%{$search}%")
                ->where('inventories.deleted_at', '=', null)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

            //total number of filtered data matching the search value request in the product table	
            $totalFiltered = DB::table('inventories')
                ->leftJoin('category_per_products', 'category_per_products.product_id', '=', 'inventories.id')
                ->leftJoin('categories', 'category_per_products.category_id', '=', 'categories.id')
                ->select('inventories.*', 'categories.category_name')
                ->orWhere('inventories.original_price', 'like', "%{$search}%")
                ->orWhere('inventories.selling_price', 'like', "%{$search}%")
                ->orWhere('inventories.quantity', 'like', "%{$search}%")
                ->orWhere('inventories.created_at', 'like', "%{$search}%")
                ->orWhere('inventories.product_name', 'like', "%{$search}%")
                ->orWhere('inventories.generic_name', 'like', "%{$search}%")
                ->orWhere('inventories.sku', 'like', "%{$search}%")
                ->orWhere('inventories.content', 'like', "%{$search}%")
                ->orWhere('categories.category_name', 'like', "%{$search}%")
                ->where('inventories.deleted_at', '=', null)
                ->count();
        }


        $data = array();

        if ($posts) {
            $inventoryLevel = InventoryLevel::all();
            //loop posts collection to transfer in another array $nestedData
            foreach ($posts as $r) {
                $status = '';
                if ($r->quantity == 0) {
                    $status = '<span title="Danger" class="badge bg-danger">Danger</span>';
                } else if ($r->quantity <= $inventoryLevel[0]->re_stock) {
                    $status = '<span title="Danger" class="badge bg-danger">Re-Stock</span>';
                } else if ($r->quantity == $inventoryLevel[0]->critical) {
                    $status = '<span title="Danger" class="badge bg-warning">Critical</span>';
                } else if ($r->quantity > $inventoryLevel[0]->critical) {
                    $status = '<span title="Danger" class="badge bg-success">Average</span>';
                }
                $nestedData['product_name'] = $r->product_name;
                $nestedData['generic_name'] = $r->generic_name;
                $nestedData['sku'] = $r->sku;
                $nestedData['content'] = $r->content;
                $nestedData['category_name'] = $r->category_name;
                $nestedData['original_price'] = $r->original_price;
                $nestedData['selling_price'] = $r->selling_price;
                $nestedData['quantity'] = $r->quantity;
                $nestedData['created_at'] = date('d-m-Y', strtotime($r->created_at));
                $nestedData['status'] = $status;
                $nestedData['action'] = '
					<button name="edit" id="edit" data-id="' . $r->id . '" class="btn bg-gradient-warning btn-sm">Edit</button>
                    <button name="delete" id="delete" data-id="' . $r->id . '" class="btn bg-gradient-danger btn-sm">Delete</button>
				';
                $data[] = $nestedData;
            }
        }

        $json_data = array(
            "draw"                => intval($request->input('draw')),
            "recordsTotal"        => intval($totalData),
            "recordsFiltered"   => intval($totalFiltered),
            "data"                => $data
        );

        //return the data in json response
        return response()->json($json_data);
    }

    public function fetchInactiveInventory(Request $request)
    {
        //column list in the table Prpducts
        $columns = array(
            0 => 'product_name',
            1 => 'generic_name',
            2 => 'sku',
            3 => 'content',
            4 => 'category_name',
            5 => 'original_price',
            6 => 'selling_price',
            7 => 'quantity',
            8 => 'quantity',
            9 => 'created_at',
            10 => 'action'
        );

        //get the total number of data in Inventory table
        $totalData = Inventory::onlyTrashed()->count();
        //total number of data that will show in the datatable default 10
        $limit = $request->input('length');
        //start number for pagination ,default 0
        $start = $request->input('start');
        //order list of the column
        $order = $columns[$request->input('order.0.column')];
        //order by ,default asc 
        $dir = $request->input('order.0.dir');



        //check if user search for a value in the inventory datatable
        if (empty($request->input('search.value'))) {
            //search dropdown
            $productName = $request->input('columns.0.search.value');
            $genericName = $request->input('columns.1.search.value');
            $sku = $request->input('columns.2.search.value');
            $categoryName = $request->input('columns.3.search.value');
            $details = $request->input('columns.4.search.value');
            $originalPrice = $request->input('columns.5.search.value');
            $sellingPrice = $request->input('columns.6.search.value');
            $stock = $request->input('columns.7.search.value');
            $status = $request->input('columns.8.search.value');
            $dateAdded = $request->input('columns.9.search.value');
            //get all the inventory data
            $posts = DB::table('inventories')
                ->leftJoin('category_per_products', 'category_per_products.product_id', '=', 'inventories.id')
                ->leftJoin('categories', 'category_per_products.category_id', '=', 'categories.id')
                ->select('inventories.*', 'categories.category_name')
                ->where('inventories.product_name', 'like', "%{$productName}%")
                ->where('inventories.generic_name', 'like', "%{$genericName}%")
                ->where('inventories.sku', 'like', "%{$sku}%")
                ->where('categories.category_name', 'like', "%{$categoryName}%")
                ->where('inventories.content', 'like', "%{$details}%")
                ->where('inventories.original_price', 'like', "%{$originalPrice}%")
                ->where('inventories.selling_price', 'like', "%{$sellingPrice}%")
                ->where('inventories.quantity', 'like', "%{$stock}%")
                ->where('inventories.created_at', 'like', "%{$dateAdded}%")
                ->where('inventories.deleted_at', '<>', null)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

            //total number of filtered data
            $totalFiltered = DB::table('inventories')
                ->leftJoin('category_per_products', 'category_per_products.product_id', '=', 'inventories.id')
                ->leftJoin('categories', 'category_per_products.category_id', '=', 'categories.id')
                ->select('inventories.*', 'categories.category_name')
                ->where('inventories.product_name', 'like', "%{$productName}%")
                ->where('inventories.generic_name', 'like', "%{$genericName}%")
                ->where('inventories.sku', 'like', "%{$sku}%")
                ->where('categories.category_name', 'like', "%{$categoryName}%")
                ->where('inventories.content', 'like', "%{$details}%")
                ->where('inventories.original_price', 'like', "%{$originalPrice}%")
                ->where('inventories.selling_price', 'like', "%{$sellingPrice}%")
                ->where('inventories.quantity', 'like', "%{$stock}%")
                ->where('inventories.created_at', 'like', "%{$dateAdded}%")
                ->where('inventories.deleted_at', '<>', null)
                ->count();
        } else {
            $search = $request->input('search.value');

            $posts = DB::table('inventories')
                ->leftJoin('category_per_products', 'category_per_products.product_id', '=', 'inventories.id')
                ->leftJoin('categories', 'category_per_products.category_id', '=', 'categories.id')
                ->select('inventories.*', 'categories.category_name')
                ->orWhere('inventories.original_price', 'like', "%{$search}%")
                ->orWhere('inventories.selling_price', 'like', "%{$search}%")
                ->orWhere('inventories.quantity', 'like', "%{$search}%")
                ->orWhere('inventories.created_at', 'like', "%{$search}%")
                ->orWhere('inventories.product_name', 'like', "%{$search}%")
                ->orWhere('inventories.generic_name', 'like', "%{$search}%")
                ->orWhere('inventories.sku', 'like', "%{$search}%")
                ->orWhere('inventories.content', 'like', "%{$search}%")
                ->orWhere('categories.category_name', 'like', "%{$search}%")
                ->where('inventories.deleted_at', '<>', null)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

            //total number of filtered data matching the search value request in the product table	
            $totalFiltered = DB::table('inventories')
                ->leftJoin('category_per_products', 'category_per_products.product_id', '=', 'inventories.id')
                ->leftJoin('categories', 'category_per_products.category_id', '=', 'categories.id')
                ->select('inventories.*', 'categories.category_name')
                ->orWhere('inventories.original_price', 'like', "%{$search}%")
                ->orWhere('inventories.selling_price', 'like', "%{$search}%")
                ->orWhere('inventories.quantity', 'like', "%{$search}%")
                ->orWhere('inventories.created_at', 'like', "%{$search}%")
                ->orWhere('inventories.product_name', 'like', "%{$search}%")
                ->orWhere('inventories.generic_name', 'like', "%{$search}%")
                ->orWhere('inventories.sku', 'like', "%{$search}%")
                ->orWhere('inventories.content', 'like', "%{$search}%")
                ->orWhere('categories.category_name', 'like', "%{$search}%")
                ->where('inventories.deleted_at', '<>', null)
                ->count();
        }


        $data = array();

        if ($posts) {
            $inventoryLevel = InventoryLevel::all();
            //loop posts collection to transfer in another array $nestedData
            foreach ($posts as $r) {
                $status = '';
                if ($r->quantity == 0) {
                    $status = '<span title="Danger" class="badge bg-danger">Danger</span>';
                } else if ($r->quantity <= $inventoryLevel[0]->re_stock) {
                    $status = '<span title="Danger" class="badge bg-danger">Re-Stock</span>';
                } else if ($r->quantity == $inventoryLevel[0]->critical) {
                    $status = '<span title="Danger" class="badge bg-warning">Critical</span>';
                } else if ($r->quantity > $inventoryLevel[0]->critical) {
                    $status = '<span title="Danger" class="badge bg-success">Average</span>';
                }
                $nestedData['product_name'] = $r->product_name;
                $nestedData['generic_name'] = $r->generic_name;
                $nestedData['sku'] = $r->sku;
                $nestedData['content'] = $r->content;
                $nestedData['category_name'] = $r->category_name;
                $nestedData['original_price'] = $r->original_price;
                $nestedData['selling_price'] = $r->selling_price;
                $nestedData['quantity'] = $r->quantity;
                $nestedData['created_at'] = date('d-m-Y', strtotime($r->created_at));
                $nestedData['status'] = $status;
                $nestedData['action'] = '
                    <button name="restore" id="restore" data-id="' . $r->id . '" class="btn bg-gradient-success btn-sm">Restore</button>
				';
                $data[] = $nestedData;
            }
        }

        $json_data = array(
            "draw"                => intval($request->input('draw')),
            "recordsTotal"        => intval($totalData),
            "recordsFiltered"   => intval($totalFiltered),
            "data"                => $data
        );

        //return the data in json response
        return response()->json($json_data);
    }

    public function fetchInventoryProducts()
    {
        try {
            $inventories = DB::table('inventories')
                ->leftJoin('category_per_products', 'category_per_products.product_id', '=', 'inventories.id')
                ->leftJoin('categories', 'category_per_products.category_id', '=', 'categories.id')
                ->select('inventories.*', 'categories.category_name')
                ->where('inventories.quantity', '>', 0)
                ->get();

            return response()->json([
                'inventories' => $inventories,
                'message' => 'Inventories Data Fetch Successful'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'data' => $e->getMessage(),
                'message' => 'Inventories Data Fetch Failed'
            ], 500);
        }
    }

    public function getInventoryAdjustmentProducts(Request $request)
    {
        //column list in the table Prpducts
        $columns = array(
            0 => 'product_name',
            1 => 'adjusted_quantity',
            2 => 'type',
            3 => 'reason',
            4 => 'created_at'
        );

        //get the total number of data in Inventory table
        $totalData = InventoryAdjustment::count();
        //total number of data that will show in the datatable default 10
        $limit = $request->input('length');
        //start number for pagination ,default 0
        $start = $request->input('start');
        //order list of the column
        $order = $columns[$request->input('order.0.column')];
        //order by ,default asc 
        $dir = $request->input('order.0.dir');
        //check if user search for a value in the inventory datatable
        if (empty($request->input('search.value'))) {
            //get all the inventory data
            $posts = DB::table('inventory_adjustments')
                ->leftJoin('inventories', 'inventory_adjustments.inventory_id', '=', 'inventories.id')
                ->leftJoin('inventory_adjustment_types', 'inventory_adjustments.inventory_adjustment_type_id', '=', 'inventory_adjustment_types.id')
                ->select('inventory_adjustments.*', 'inventories.product_name', 'inventory_adjustment_types.type')
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

            //total number of filtered data
            $totalFiltered =  DB::table('inventory_adjustments')
                ->leftJoin('inventories', 'inventory_adjustments.inventory_id', '=', 'inventories.id')
                ->leftJoin('inventory_adjustment_types', 'inventory_adjustments.inventory_adjustment_type_id', '=', 'inventory_adjustment_types.id')
                ->select('inventory_adjustments.*', 'inventories.product_name', 'inventory_adjustment_types.type')
                ->count();
        } else {
            $search = $request->input('search.value');

            $posts =  DB::table('inventory_adjustments')
                ->leftJoin('inventories', 'inventory_adjustments.inventory_id', '=', 'inventories.id')
                ->leftJoin('inventory_adjustment_types', 'inventory_adjustments.inventory_adjustment_type_id', '=', 'inventory_adjustment_types.id')
                ->select('inventory_adjustments.*', 'inventories.product_name', 'inventory_adjustment_types.type')
                ->orWhere('inventories.product_name', 'like', "%{$search}%")
                ->orWhere('inventory_adjustments.adjusted_quantity', 'like', "%{$search}%")
                ->orWhere('inventory_adjustment_types.type', 'like', "%{$search}%")
                ->orWhere('inventory_adjustments.reason', 'like', "%{$search}%")
                ->orWhere('inventory_adjustments.created_at', 'like', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

            //total number of filtered data matching the search value request in the product table	
            $totalFiltered = DB::table('inventory_adjustments')
                ->leftJoin('inventories', 'inventory_adjustments.inventory_id', '=', 'inventories.id')
                ->leftJoin('inventory_adjustment_types', 'inventory_adjustments.inventory_adjustment_type_id', '=', 'inventory_adjustment_types.id')
                ->select('inventory_adjustments.*', 'inventories.product_name', 'inventory_adjustment_types.type')
                ->orWhere('inventories.product_name', 'like', "%{$search}%")
                ->orWhere('inventory_adjustments.adjusted_quantity', 'like', "%{$search}%")
                ->orWhere('inventory_adjustment_types.type', 'like', "%{$search}%")
                ->orWhere('inventory_adjustments.reason', 'like', "%{$search}%")
                ->orWhere('inventory_adjustments.created_at', 'like', "%{$search}%")
                ->count();
        }


        $data = array();

        if ($posts) {
            //loop posts collection to transfer in another array $nestedData
            foreach ($posts as $r) {
                $nestedData['product_name'] = $r->product_name;
                $nestedData['adjusted_quantity'] = $r->adjusted_quantity;
                $nestedData['type'] = $r->type;
                $nestedData['reason'] = $r->reason;
                $nestedData['created_at'] = date('d-m-Y', strtotime($r->created_at));
                $data[] = $nestedData;
            }
        }

        $json_data = array(
            "draw"                => intval($request->input('draw')),
            "recordsTotal"        => intval($totalData),
            "recordsFiltered"   => intval($totalFiltered),
            "data"                => $data
        );

        //return the data in json response
        return response()->json($json_data);
    }

    public function getSalesLogs(Request $request)
    {
        //column list in the table Prpducts
        $columns = array(
            0 => 'product_name',
            1 => 'quantity',
            2 => 'price',
            3 => 'price',
            4 => 'created_at'
        );

        //get the total number of data in Inventory table
        $totalData = SaleItem::count();
        //total number of data that will show in the datatable default 10
        $limit = $request->input('length');
        //start number for pagination ,default 0
        $start = $request->input('start');
        //order list of the column
        $order = $columns[$request->input('order.0.column')];
        //order by ,default asc 
        $dir = $request->input('order.0.dir');
        //check if user search for a value in the inventory datatable
        if (empty($request->input('search.value'))) {
            //get all the inventory data
            $posts = DB::table('sale_items')
                ->leftJoin('inventories', 'sale_items.inventory_id', '=', 'inventories.id')
                ->select('sale_items.*', 'inventories.product_name')
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

            //total number of filtered data
            $totalFiltered =  DB::table('sale_items')
                ->leftJoin('inventories', 'sale_items.inventory_id', '=', 'inventories.id')
                ->select('sale_items.*', 'inventories.product_name')
                ->count();
        } else {
            $search = $request->input('search.value');

            $posts =  DB::table('sale_items')
                ->leftJoin('inventories', 'sale_items.inventory_id', '=', 'inventories.id')
                ->select('sale_items.*', 'inventories.product_name')
                ->orWhere('inventories.product_name', 'like', "%{$search}%")
                ->orWhere('sale_items.quantity', 'like', "%{$search}%")
                ->orWhere('sale_items.price', 'like', "%{$search}%")
                ->orWhere('sale_items.created_at', 'like', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

            //total number of filtered data matching the search value request in the product table	
            $totalFiltered =  DB::table('sale_items')
                ->leftJoin('inventories', 'sale_items.inventory_id', '=', 'inventories.id')
                ->select('sale_items.*', 'inventories.product_name')
                ->orWhere('inventories.product_name', 'like', "%{$search}%")
                ->orWhere('sale_items.quantity', 'like', "%{$search}%")
                ->orWhere('sale_items.price', 'like', "%{$search}%")
                ->orWhere('sale_items.created_at', 'like', "%{$search}%")
                ->count();
        }


        $data = array();

        if ($posts) {
            //loop posts collection to transfer in another array $nestedData
            foreach ($posts as $r) {
                $total_sales_price = $r->price * $r->quantity;

                $nestedData['product_name'] = $r->product_name;
                $nestedData['quantity'] = $r->quantity;
                $nestedData['price'] = $r->price;
                $nestedData['total_sales_price'] = number_format($total_sales_price, 2);
                $nestedData['created_at'] = date('d-m-Y', strtotime($r->created_at));
                $data[] = $nestedData;
            }
        }

        $json_data = array(
            "draw"                => intval($request->input('draw')),
            "recordsTotal"        => intval($totalData),
            "recordsFiltered"   => intval($totalFiltered),
            "data"                => $data
        );

        //return the data in json response
        return response()->json($json_data);
    }
}
