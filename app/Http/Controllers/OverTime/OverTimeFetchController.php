<?php

namespace App\Http\Controllers\OverTime;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\OverTime;
use Carbon\Carbon;

class OverTimeFetchController extends Controller
{
    public function fetchOverTime(Request $request)
	{
		//column list in the table Prpducts
		$columns = array(
			0 => 'overtime_date',
			1 => 'duration'
		);

        $deployment_id = $request->deployment_id;

		//get the total number of data in User table
		$totalData = OverTime::where([
            ['over_times.deployment_id', $deployment_id],
            ['over_times.deleted_at', '=', null]
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
			$posts = OverTime::select('over_times.overtime_date','over_times.duration','over_times.id as id')
                ->where([
                    ['over_times.deployment_id', $deployment_id],
                    ['over_times.deleted_at', '=', null]
                ])
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data
			$totalFiltered = OverTime::where([
                ['over_times.deployment_id', $deployment_id],
                ['over_times.deleted_at', '=', null]
            ])->count();
		} else {
			$search = $request->input('search.value');

			$posts = OverTime::where(function ($query) use ($search) {
                $query->orWhere('overtime_date', 'like', "%{$search}%")
					->orWhere('duration', 'like', "%{$search}%");
			})
            ->where([
                ['over_times.deployment_id', $deployment_id],
                ['over_times.deleted_at', '=', null]
            ])
            ->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

			//total number of filtered data matching the search value request in the Supplier table	
			$totalFiltered = OverTime::where(function ($query) use ($search) {
                $query->orWhere('overtime_date', 'like', "%{$search}%")
                    ->orWhere('duration', 'like', "%{$search}%");
                })
                ->where([
                    ['over_times.deployment_id', $deployment_id],
                    ['over_times.deleted_at', '=', null]
                ])->count();
		}


		$data = array();

		if ($posts) {
			//loop posts collection to transfer in another array $nestedData
			foreach ($posts as $r) {
                $carbonTime = Carbon::createFromFormat('H:i:s', $r->duration);
                $totalMinutes = $carbonTime->hour * 60 + $carbonTime->minute;
				$nestedData['overtime_date'] =  date('d-m-Y', strtotime($r->overtime_date));
				$nestedData['duration'] = $totalMinutes." mins";
                $nestedData['action'] = '
                    <button name="delete" id="delete" data-id="' . $r->id . '" class="btn bg-gradient-danger btn-sm">Archive</button>
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
