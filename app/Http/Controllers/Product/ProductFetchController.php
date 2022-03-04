<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Inventory;

class ProductFetchController extends Controller
{
	public function fetchProduct(Request $request)
	{
		//column list in the table Prpducts
		$columns = array(
			0 => 'product_name',
			1 => 'generic_name',
			2 => 'sku',
			3 => 'category_name',
			4 => 'content',
			5 => 'image',
			6 => 'created_at',
			7 => 'action'
		);

		//get the total number of data in Product table
		$totalData = Inventory::count();
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
			$posts = Inventory::with('categories')
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data
			$totalFiltered = Inventory::count();
		} else {
			$search = $request->input('search.value');

			$posts = Inventory::where('product_name', 'like', "%{$search}%")
				->orWhereHas('categories', function ($query) use ($search) {
					$query->where('category_name', 'like', "%{$search}%");
				})
				->orWhere('generic_name', 'like', "%{$search}%")
				->orWhere('sku', 'like', "%{$search}%")
				->orWhere('content', 'like', "%{$search}%")
				->orWhere('image', 'like', "%{$search}%")
				->orWhere('created_at', 'like', "%{$search}%")
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data matching the search value request in the product table	
			$totalFiltered = Inventory::where('product_name', 'like', "%{$search}%")
				->orWhereHas('categories', function ($query) use ($search) {
					$query->where('category_name', 'like', "%{$search}%");
				})
				->orWhere('generic_name', 'like', "%{$search}%")
				->orWhere('sku', 'like', "%{$search}%")
				->orWhere('content', 'like', "%{$search}%")
				->orWhere('image', 'like', "%{$search}%")
				->orWhere('created_at', 'like', "%{$search}%")
				->count();
		}


		$data = array();

		if ($posts) {
			//loop posts collection to transfer in another array $nestedData
			foreach ($posts as $r) {
				$imageUrl = $r->image == null ? 'http://w3adda.com/wp-content/uploads/2019/09/No_Image-128.png' : url('/') . '/images/' . $r->id . '/' . $r->image;
				$nestedData['product_name'] = $r->product_name;
				$nestedData['generic_name'] = $r->generic_name;
				$nestedData['sku'] = $r->sku;
				$nestedData['category_name'] = $r->categories[0]->category_name;
				$nestedData['content'] = $r->content;
				$nestedData['image'] = '<img src="' . $imageUrl . '" width="50" height="50">';
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
}
