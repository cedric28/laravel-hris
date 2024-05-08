<?php

namespace App\Http\Controllers\Late;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\LateTime;
use Carbon\Carbon;

class LateFetchController extends Controller
{
    public function fetchLate(Request $request)
	{
		//column list in the table Prpducts
		$columns = array(
			0 => 'latetime_date',
			1 => 'duration'
		);

        $deployment_id = $request->deployment_id;

		//get the total number of data in User table
		$totalData = LateTime::where([
            ['late_times.deployment_id', $deployment_id],
            ['late_times.deleted_at', '=', null]
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
			$posts = LateTime::select('late_times.latetime_date','late_times.duration','late_times.id as id')
                ->where([
                    ['late_times.deployment_id', $deployment_id],
                    ['late_times.deleted_at', '=', null]
                ])
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data
			$totalFiltered = LateTime::where([
                ['late_times.deployment_id', $deployment_id],
                ['late_times.deleted_at', '=', null]
            ])->count();
		} else {
			$search = $request->input('search.value');

			$posts = LateTime::where(function ($query) use ($search) {
                $query->orWhere('latetime_date', 'like', "%{$search}%")
					->orWhere('duration', 'like', "%{$search}%");
			})
            ->where([
                ['late_times.deployment_id', $deployment_id],
                ['late_times.deleted_at', '=', null]
            ])
            ->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

			//total number of filtered data matching the search value request in the Supplier table	
			$totalFiltered = LateTime::where(function ($query) use ($search) {
                $query->orWhere('latetime_date', 'like', "%{$search}%")
                    ->orWhere('duration', 'like', "%{$search}%");
                })
                ->where([
                    ['late_times.deployment_id', $deployment_id],
                    ['late_times.deleted_at', '=', null]
                ])->count();
		}


		$data = array();

		if ($posts) {
			//loop posts collection to transfer in another array $nestedData
			foreach ($posts as $r) {
				$nestedData['latetime_date'] =  date('d-m-Y', strtotime($r->latetime_date));
				$nestedData['duration'] = Carbon::parse($r->duration)->format('i')." mins";
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
