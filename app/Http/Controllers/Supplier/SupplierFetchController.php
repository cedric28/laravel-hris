<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Supplier;
use Carbon\Carbon;

class SupplierFetchController extends Controller
{
    public function fetchSupplier(Request $request)
    {
		//column list in the table Prpducts
        $columns = array(
			0 => 'name',
			1 => 'short_name',
			2 => 'contact_number',
			3 => 'email',
			4 => 'address',
			5 => 'created_at',
			6 => 'action'
		);
		
		//get the total number of data in Supplier table
		$totalData = Supplier::count();
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
			//get all the Supplier data
			$posts = Supplier::offset($start)
					->limit($limit)
					->orderBy($order,$dir)
					->get();
			
			//total number of filtered data
			$totalFiltered = Supplier::count();
		}else{
            $search = $request->input('search.value');
            
			$posts = Supplier::where('name', 'like', "%{$search}%")
							->orWhere('short_name','like',"%{$search}%")
							->orWhere('address','like',"%{$search}%")
							->orWhere('contact_number','like',"%{$search}%")
							->orWhere('email','like',"%{$search}%")
							->offset($start)
							->limit($limit)
							->orderBy($order, $dir)
							->get();

			//total number of filtered data matching the search value request in the Supplier table	
            $totalFiltered = Supplier::where('name', 'like', "%{$search}%")
							->orWhere('short_name','like',"%{$search}%")
							->orWhere('address','like',"%{$search}%")
							->orWhere('contact_number','like',"%{$search}%")
							->orWhere('email','like',"%{$search}%")
							->count();
		}		
					
		
		$data = array();
		
		if($posts){
			//loop posts collection to transfer in another array $nestedData
			foreach($posts as $r){
				$nestedData['name'] = $r->name;
				$nestedData['short_name'] = $r->short_name;
				$nestedData['contact_number'] = $r->contact_number;
				$nestedData['email'] = $r->email;
				$nestedData['address'] = $r->address;
				$nestedData['created_at'] = date('d-m-Y',strtotime($r->created_at));
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
