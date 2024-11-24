<?php

namespace App\Http\Controllers\BestPerformer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Feedback;
use Validator, Hash, DB;
use Carbon\Carbon;

class BestPerformerFetchController extends Controller
{
    public function fetchBestPerformer(Request $request)
	{
        $current_month = now()->startOfMonth()->format('Y-m');
		$currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $endOfMonth = Carbon::now()->endOfMonth();
		$threeDaysBeforeEndOfMonth = $endOfMonth->subDays(3);
		//column list in the table Prpducts
		$columns = array(
			0 => 'first_name',
			1 => 'client_name',
			2 => 'rate',
			3 => 'action'
		);

		//get the total number of data in User table
		$totalData = Feedback::select('feedback.id as id')
        ->join('deployments', 'deployments.id', '=', 'feedback.deployment_id')
        ->whereYear('feedback.created_at', $currentYear)
        ->whereHas('deployment.attendances', function ($query) use ($current_month, $threeDaysBeforeEndOfMonth) {
            $query->whereBetween('attendance_date', [$current_month . '-01', $current_month . '-31'])
                            ->whereNotIn('day_of_week', [6, 0])
                            ->whereDate('attendance_date', '<=', $threeDaysBeforeEndOfMonth);
        })
        ->whereDoesntHave('deployment.lates', function ($query) use ($currentMonth) {
            $query->whereMonth('created_at', $currentMonth);
        })
        ->whereDoesntHave('deployment.attendances', function ($query) {
            $query->where('status', 'Absent');
        })
        ->where([
            ['feedback.rate','=',10],
            ['deployments.status','=','new']
        ])
        ->count();
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
            $posts = Feedback::select('feedback.id as id',DB::raw('CONCAT(employees.last_name, ", ", employees.first_name, " ", employees.middle_name) AS full_name'), 'clients.name as company','feedback.rate')
            ->join('deployments', 'deployments.id', '=', 'feedback.deployment_id')
            ->join('employees', 'deployments.employee_id', '=', 'employees.id')
            ->join('clients', 'deployments.client_id', '=', 'clients.id')
            ->whereYear('feedback.created_at', $currentYear)
            ->whereHas('deployment.attendances', function ($query) use ($current_month, $threeDaysBeforeEndOfMonth) {
                $query->whereBetween('attendance_date', [$current_month . '-01', $current_month . '-31'])
                                ->whereNotIn('day_of_week', [6, 0])
                                ->whereDate('attendance_date', '<=', $threeDaysBeforeEndOfMonth);
            })
            ->whereDoesntHave('deployment.lates', function ($query) use ($currentMonth) {
                $query->whereMonth('created_at', $currentMonth);
            })
            ->whereDoesntHave('deployment.attendances', function ($query) {
                $query->where('status', 'Absent');
            })
            ->where([
                ['feedback.rate','=',10],
                ['deployments.status','=','new']
            ])
            ->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

			//total number of filtered data
			$totalFiltered = Feedback::select('feedback.id as id',DB::raw('CONCAT(employees.last_name, ", ", employees.first_name, " ", employees.middle_name) AS full_name'), 'clients.name as company','feedback.rate')
            ->join('deployments', 'deployments.id', '=', 'feedback.deployment_id')
            ->join('employees', 'deployments.employee_id', '=', 'employees.id')
            ->join('clients', 'deployments.client_id', '=', 'clients.id')
            ->whereYear('feedback.created_at', $currentYear)
            ->whereHas('deployment.attendances', function ($query) use ($current_month, $threeDaysBeforeEndOfMonth) {
                $query->whereBetween('attendance_date', [$current_month . '-01', $current_month . '-31'])
                                ->whereNotIn('day_of_week', [6, 0])
                                ->whereDate('attendance_date', '<=', $threeDaysBeforeEndOfMonth);
            })
            ->whereDoesntHave('deployment.lates', function ($query) use ($currentMonth) {
                $query->whereMonth('created_at', $currentMonth);
            })
            ->whereDoesntHave('deployment.attendances', function ($query) {
                $query->where('status', 'Absent');
            })
            ->where([
                ['feedback.rate','=',10],
                ['deployments.status','=','new']
            ])->count();

		} else {
			$search = $request->input('search.value');

            $posts = Feedback::select('feedback.id as id',DB::raw('CONCAT(employees.last_name, ", ", employees.first_name, " ", employees.middle_name) AS full_name'), 'clients.name as company','feedback.rate')
                ->join('deployments', 'deployments.id', '=', 'feedback.deployment_id')
                ->join('employees', 'deployments.employee_id', '=', 'employees.id')
                ->join('clients', 'deployments.client_id', '=', 'clients.id')
                ->orWhere('employees.first_name', 'like', "%{$search}%")
                ->orWhere('employees.middle_name', 'like', "%{$search}%")
                ->orWhere('employees.last_name', 'like', "%{$search}%")
				->orWhere('clients.name', 'like', "%{$search}%")
                ->whereYear('feedback.created_at', $currentYear)
                ->whereHas('deployment.attendances', function ($query) use ($current_month, $threeDaysBeforeEndOfMonth) {
                    $query->whereBetween('attendance_date', [$current_month . '-01', $current_month . '-31'])
                                    ->whereNotIn('day_of_week', [6, 0])
                                    ->whereDate('attendance_date', '<=', $threeDaysBeforeEndOfMonth);
                })
                ->whereDoesntHave('deployment.lates', function ($query) use ($currentMonth) {
                    $query->whereMonth('created_at', $currentMonth);
                })
                ->whereDoesntHave('deployment.attendances', function ($query) {
                    $query->where('status', 'Absent');
                })
                ->where([
                    ['feedback.rate','=',10],
                    ['deployments.status','=','new']
                ])
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			//total number of filtered data matching the search value request in the Supplier table	
			$totalFiltered = Feedback::select('feedback.id as id',DB::raw('CONCAT(employees.last_name, ", ", employees.first_name, " ", employees.middle_name) AS full_name'), 'clients.name as company','feedback.rate')
                    ->join('deployments', 'deployments.id', '=', 'feedback.deployment_id')
                    ->join('employees', 'deployments.employee_id', '=', 'employees.id')
                    ->join('clients', 'deployments.client_id', '=', 'clients.id')
                    ->orWhere('employees.first_name', 'like', "%{$search}%")
                    ->orWhere('employees.middle_name', 'like', "%{$search}%")
                    ->orWhere('employees.last_name', 'like', "%{$search}%")
                    ->orWhere('clients.name', 'like', "%{$search}%")
                    ->whereYear('feedback.created_at', $currentYear)
                    ->whereHas('deployment.attendances', function ($query) use ($current_month, $threeDaysBeforeEndOfMonth) {
                        $query->whereBetween('attendance_date', [$current_month . '-01', $current_month . '-31'])
                                        ->whereNotIn('day_of_week', [6, 0])
                                        ->whereDate('attendance_date', '<=', $threeDaysBeforeEndOfMonth);
                    })
                    ->whereDoesntHave('deployment.attendances', function ($query) {
                        $query->where('status', 'Absent');
                    })
                    ->whereDoesntHave('deployment.lates', function ($query) use ($currentMonth) {
                        $query->whereMonth('created_at', $currentMonth);
                    })
                    ->where([
                        ['feedback.rate','=',10],
                        ['deployments.status','=','new']
                    ])
				    ->count();
		}


		$data = array();

		if ($posts) {
			//loop posts collection to transfer in another array $nestedData
			foreach ($posts as $r) {
				$nestedData['fullname'] = $r->full_name;
				$nestedData['client_name'] = $r->company;
				$nestedData['rate'] = $r->rate;
				$nestedData['action'] = '
                <button name="generate_pdf" id="generate_pdf" title="generate pdf" data-id="' . $r->id . '" class="btn bg-gradient-info btn-sm">Generate Certificate</button>
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
