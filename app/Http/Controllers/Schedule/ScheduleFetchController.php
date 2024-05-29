<?php

namespace App\Http\Controllers\Schedule;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Schedule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ScheduleFetchController extends Controller
{
	public function fetchSchedule(Request $request)
	{
		//column list in the table Prpducts
		$columns = array(
			0 => 'employee_name',
			1 => 'slug',
			2 => 'time_in',
			3 => 'time_out',
			4 => 'action'
		);

		//get the total number of data in User table
		$totalData = Schedule::count();
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
			$posts = Schedule::select('schedules.id as id','employees.name as employee_name','schedules.slug','schedules.time_in', 'schedules.time_out')
				->join('deployments', 'schedules.deployment_id', '=', 'deployments.id')
				->join('employees', 'deployments.employee_id', '=', 'employees.id')
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data
			$totalFiltered = Schedule::count();
		} else {
			$search = $request->input('search.value');

			$posts =  DB::table('schedules')
            ->leftJoin('deployments', 'schedules.deployment_id', '=', 'deployments.id')
		    ->leftJoin('employees', 'deployments.employee_id', '=', 'employees.id')
            ->select('schedules.id as id','employees.name as employee_name','schedules.slug','schedules.time_in', 'schedules.time_out')
            ->where(function ($query) use ($search) {
                $query->where('employees.name', 'like', "%{$search}%")
                    ->orWhere('schedules.slug', 'like', "%{$search}%")
                    ->orWhere('schedules.time_in', 'like', "%{$search}%")
                    ->orWhere('schedules.time_out', 'like', "%{$search}%");
            })
            ->where('schedules.deleted_at', '=', null)
            ->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

			//total number of filtered data matching the search value request in the Supplier table	
			$totalFiltered = DB::table('schedules')
            ->leftJoin('deployments', 'schedules.deployment_id', '=', 'deployments.id')
		    ->leftJoin('employees', 'deployments.employee_id', '=', 'employees.id')
            ->select('schedules.id as id','employees.name as employee_name','schedules.slug','schedules.time_in', 'schedules.time_out')
            ->where(function ($query) use ($search) {
                $query->where('employees.name', 'like', "%{$search}%")
                    ->orWhere('schedules.slug', 'like', "%{$search}%")
                    ->orWhere('schedules.time_in', 'like', "%{$search}%")
                    ->orWhere('schedules.time_out', 'like', "%{$search}%");
            })
            ->where('schedules.deleted_at', '=', null)
            ->count();
		}


		$data = array();

		if ($posts) {
			//loop posts collection to transfer in another array $nestedData
			foreach ($posts as $r) {
				$nestedData['employee_name'] = $r->employee_name;
				$nestedData['slug'] = $r->slug;
				$nestedData['time_in']  = Carbon::parse($r->time_in)->format('g:i A');
				$nestedData['time_out'] = Carbon::parse($r->time_out)->format('g:i A');
				$nestedData['action'] = '
						<button name="show" id="show" data-id="' . $r->id . '" class="btn bg-gradient-primary btn-sm"><i class="nav-icon fas fa-eye"></i></button>
						<button name="edit" id="edit" data-id="' . $r->id . '" class="btn bg-gradient-warning btn-sm"><i class="fas fa-pencil-alt"></i></button>
						<button name="delete" id="delete" data-id="' . $r->id . '" class="btn bg-gradient-danger btn-sm"><i class="fas fa-trash"></i></button>
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

	public function fetchInactiveSchedule(Request $request)
	{
	//column list in the table Prpducts
	$columns = array(
        0 => 'employee_name',
        1 => 'slug',
        2 => 'time_in',
        3 => 'time_out',
        4 => 'action'
    );

	//get the total number of data in User table
	$totalData = Schedule::onlyTrashed()->count();
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
		$posts = Schedule::onlyTrashed()->select('schedules.id as id','employees.name as employee_name','schedules.slug','schedules.time_in', 'schedules.time_out')
				->join('deployments', 'schedules.deployment_id', '=', 'deployments.id')
				->join('employees', 'deployments.employee_id', '=', 'employees.id')
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

		//total number of filtered data
		$totalFiltered = Schedule::onlyTrashed()->count();
	} else {
		$search = $request->input('search.value');
        $posts =  DB::table('schedules')
        ->leftJoin('deployments', 'schedules.deployment_id', '=', 'deployments.id')
        ->leftJoin('employees', 'deployments.employee_id', '=', 'employees.id')
        ->select('schedules.id as id','employees.name as employee_name','schedules.slug as schedule','schedules.time_in', 'schedules.time_out')
        ->where(function ($query) use ($search) {
            $query->where('employees.name', 'like', "%{$search}%")
                ->orWhere('schedules.slug', 'like', "%{$search}%")
                ->orWhere('schedules.time_in', 'like', "%{$search}%")
                ->orWhere('schedules.time_out', 'like', "%{$search}%");
        })
        ->where('schedules.deleted_at', '<>', null)
        ->offset($start)
        ->limit($limit)
        ->orderBy($order, $dir)
        ->get();

        //total number of filtered data matching the search value request in the Supplier table	
        $totalFiltered = DB::table('schedules')
        ->leftJoin('deployments', 'schedules.deployment_id', '=', 'deployments.id')
        ->leftJoin('employees', 'deployments.employee_id', '=', 'employees.id')
        ->select('schedules.id as id','employees.name as employee_name','schedules.slug','schedules.time_in', 'schedules.time_out')
        ->where(function ($query) use ($search) {
            $query->where('employees.name', 'like', "%{$search}%")
                ->orWhere('schedules.slug', 'like', "%{$search}%")
                ->orWhere('schedules.time_in', 'like', "%{$search}%")
                ->orWhere('schedules.time_out', 'like', "%{$search}%");
        })
        ->where('schedules.deleted_at', '<>', null)
        ->count();
	}


	$data = array();

		if ($posts) {
			//loop posts collection to transfer in another array $nestedData
			foreach ($posts as $r) {
				$nestedData['employee_name'] = $r->employee_name;
				$nestedData['slug'] = $r->slug;
				$nestedData['time_in'] = $r->time_in;
				$nestedData['time_out'] = $r->time_out;
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

