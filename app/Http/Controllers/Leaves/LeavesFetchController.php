<?php

namespace App\Http\Controllers\Leaves;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Leave;

class LeavesFetchController extends Controller
{
  public function fetchLeaves(Request $request){
		//column list in the table Prpducts
		$columns = array(
			0 => 'leave_type',
			1 => 'leave_date',
			2 => 'leave_time',
			3 => 'action'
		);

		$deployment_id = $request->deployment_id;

		//get the total number of data in User table
		$totalData = Leave::where([
			['deployment_id', $deployment_id],
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

		//check if user search for a value in the User datatable
		if (empty($request->input('search.value'))) {
			//get all the User data
			$posts = Leave::select('leave_types.name as leave_type','leaves.leave_date','leaves.leave_time','leaves.id as id')
				->join('leave_types', 'leave_types.id', '=', 'leaves.leave_type_id')
				->where([
					['leaves.deployment_id', $deployment_id],
					['leaves.deleted_at', '=', null]
				])
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data
			$totalFiltered = Leave::where([
				['leaves.deployment_id', $deployment_id],
				['leaves.deleted_at', '=', null]
			])->count();
		} else {
			$search = $request->input('search.value');

			$posts = Leave::where(function ($query) use ($search) {
				$query->whereHas('leave_type', function ($query) use ($search) {
					$query->where('name', 'like', "%{$search}%");
				})
					->orWhere('leave_date', 'like', "%{$search}%")
					->orWhere('leave_time', 'like', "%{$search}%");
			})
			->where([
				['leaves.deployment_id', $deployment_id],
				['leaves.deleted_at', '=', null]
			])
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data matching the search value request in the Supplier table	
			$totalFiltered = Leave::where(function ($query) use ($search) {
				$query->whereHas('leave_type', function ($query) use ($search) {
					$query->where('name', 'like', "%{$search}%");
				})
					->orWhere('leave_date', 'like', "%{$search}%")
					->orWhere('leave_time', 'like', "%{$search}%");
			})
			->where([
				['leaves.deployment_id', $deployment_id],
				['leaves.deleted_at', '=', null]
			])
				->count();
		}


		$data = array();

		if ($posts) {
			//loop posts collection to transfer in another array $nestedData
			foreach ($posts as $r) {
				$nestedData['leave_type'] = $r->leave_type;
				$nestedData['leave_date'] = date('d-m-Y', strtotime($r->leave_date));
				$nestedData['leave_time'] = $r->leave_time;
				$nestedData['action'] = '
						<button name="delete" id="delete_leaves" data-id="' . $r->id . '" class="btn bg-gradient-danger btn-sm"><i class="fas fa-trash"></i></button>
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
