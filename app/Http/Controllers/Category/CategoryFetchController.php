<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Category;
use Carbon\Carbon;

class CategoryFetchController extends Controller
{
	public function fetchCategory(Request $request)
	{
		//column list in the table Prpducts
		$columns = array(
			0 => 'category_name',
			1 => 'created_at',
			2 => 'action'
		);

		//get the total number of data in Category table
		$totalData = Category::count();
		//total number of data that will show in the datatable default 10
		$limit = $request->input('length');
		//start number for pagination ,default 0
		$start = $request->input('start');
		//order list of the column
		$order = $columns[$request->input('order.0.column')];
		//order by ,default asc 
		$dir = $request->input('order.0.dir');

		//check if user search for a value in the Category datatable
		if (empty($request->input('search.value'))) {
			//get all the category data
			$posts = Category::offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data
			$totalFiltered = Category::count();
		} else {
			$search = $request->input('search.value');

			$posts = Category::where('category_name', 'like', "%{$search}%")
				->orWhere('created_at', 'like', "%{$search}%")
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data matching the search value request in the Category table	
			$totalFiltered = Category::where('category_name', 'like', "%{$search}%")
				->orWhere('created_at', 'like', "%{$search}%")
				->count();
		}


		$data = array();

		if ($posts) {
			//loop posts collection to transfer in another array $nestedData
			foreach ($posts as $r) {
				$nestedData['category_name'] = $r->category_name;
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

	public function fetchInactiveCategory(Request $request)
	{
		//column list in the table Prpducts
		$columns = array(
			0 => 'category_name',
			1 => 'created_at',
			2 => 'action'
		);

		//get the total number of data in Category table
		$totalData = Category::onlyTrashed()->count();
		//total number of data that will show in the datatable default 10
		$limit = $request->input('length');
		//start number for pagination ,default 0
		$start = $request->input('start');
		//order list of the column
		$order = $columns[$request->input('order.0.column')];
		//order by ,default asc 
		$dir = $request->input('order.0.dir');

		//check if user search for a value in the Category datatable
		if (empty($request->input('search.value'))) {
			//get all the category data
			$posts = Category::onlyTrashed()
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data
			$totalFiltered = Category::onlyTrashed()->count();
		} else {
			$search = $request->input('search.value');

			$posts = Category::onlyTrashed()
				->orWhere('category_name', 'like', "%{$search}%")
				->orWhere('created_at', 'like', "%{$search}%")
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data matching the search value request in the Category table	
			$totalFiltered = Category::onlyTrashed()
				->orWhere('category_name', 'like', "%{$search}%")
				->orWhere('created_at', 'like', "%{$search}%")
				->count();
		}


		$data = array();

		if ($posts) {
			//loop posts collection to transfer in another array $nestedData
			foreach ($posts as $r) {
				$nestedData['category_name'] = $r->category_name;
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
