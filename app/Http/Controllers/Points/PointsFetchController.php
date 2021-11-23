<?php

namespace App\Http\Controllers\Points;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Point;

class PointsFetchController extends Controller
{
    public function fetchPoint(Request $request)
    {
		//column list in the table Prpducts
        $columns = array(
			0 => 'point_name',
			1 => 'discount_rate',
			2 => 'point',
			3 => 'price_per_point',
			4 => 'total_needed_point',
			5 => 'created_at',
			6 => 'action'
		);
		
		//get the total number of data in point table
		$totalData = Point::count();
		//total number of data that will show in the datatable default 10
		$limit = $request->input('length');
		//start number for pagination ,default 0
		$start = $request->input('start');
		//order list of the column
		$order = $columns[$request->input('order.0.column')];
		//order by ,default asc 
		$dir = $request->input('order.0.dir');
		
		//check if user search for a value in the point datatable
		if(empty($request->input('search.value'))){
			//get all the point data
			$posts = Point::offset($start)
					->limit($limit)
					->orderBy($order,$dir)
					->get();
			
			//total number of filtered data
			$totalFiltered = Point::count();
		}else{
            $search = $request->input('search.value');
            
			$posts = Point::where('point_name', 'like', "%{$search}%")
							->orWhere('discount_rate','like',"%{$search}%")
							->orWhere('point','like',"%{$search}%")
							->orWhere('price_per_point','like',"%{$search}%")
							->orWhere('total_needed_point','like',"%{$search}%")
							->orWhere('created_at','like',"%{$search}%")
							->offset($start)
							->limit($limit)
							->orderBy($order, $dir)
							->get();

			//total number of filtered data matching the search value request in the Category table	
            $totalFiltered = Point::where('point_name', 'like', "%{$search}%")
							->orWhere('discount_rate','like',"%{$search}%")
							->orWhere('point','like',"%{$search}%")
							->orWhere('price_per_point','like',"%{$search}%")
							->orWhere('total_needed_point','like',"%{$search}%")
							->orWhere('created_at','like',"%{$search}%")
							->count();
		}		
					
		
		$data = array();
		
		if($posts){
			//loop posts collection to transfer in another array $nestedData
			foreach($posts as $r){
				$nestedData['point_name'] = $r->point_name;
				$nestedData['discount_rate'] = $r->discount_rate;
				$nestedData['point'] = $r->point;
				$nestedData['price_per_point'] = $r->price_per_point;
				$nestedData['total_needed_point'] = $r->total_needed_point;
				$nestedData['created_at'] = date('d-m-Y',strtotime($r->created_at));
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

	public function fetchPointDiscount()
	{
		try {
			$customerPoints = Point::all();

            return response()->json([
                    'customerPoints' => $customerPoints,
                    'message' => 'Customer Points Data Fetch Successful'
                ], 200);
            
        } catch(\Exception $e) {
            return response()->json([
                'data' => $e->getMessage(),
                'message' => 'Customer Points Data Fetch Failed'
            ], 500); 
        }
	}
}
