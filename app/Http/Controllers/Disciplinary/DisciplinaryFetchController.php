<?php

namespace App\Http\Controllers\Disciplinary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DisciplinaryFetchController extends Controller
{
    public function fetchForTermination(Request $request)
	{
        $currentYear = Carbon::now()->year;
		//column list in the table Prpducts
		$columns = array(
			0 => 'first_name',
			1 => 'client_name',
			2 => 'action'
		);

		//get the total number of data in User table
		$totalData =  Deployment::select(
		DB::raw('CONCAT(employees.last_name, ", ", employees.first_name, " ", employees.middle_name) AS full_name'), 'clients.name as company')
		->join('employees', 'deployments.employee_id', '=', 'employees.id')
		->join('clients', 'deployments.client_id', '=', 'clients.id')
		->where('status','new')
		->where(function($query) {
			$query->where('lates.latetime_date', '>=', DB::raw('DATE_SUB(deployments.start_date, INTERVAL 11 MONTH)'))
									->orWhere('attendances.status', 'Absent')
									->where('attendances.attendance_date', '>=', DB::raw('DATE_SUB(deployments.start_date, INTERVAL 12 MONTH)'));
})
->groupBy('deployments.id') // Add this if you need to group by employee ID
->havingRaw('COUNT(DISTINCT lates.id) >= 10')
->havingRaw('COUNT(CASE WHEN attendances.status = "Absent" THEN 1 END) >= 10')
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
            $posts = Deployment::select(
            DB::raw('CONCAT(employees.last_name, ", ", employees.first_name, " ", employees.middle_name) AS full_name'), 'clients.name as company')
            ->join('employees', 'deployments.employee_id', '=', 'employees.id')
            ->join('clients', 'deployments.client_id', '=', 'clients.id')
            ->where('status','new')
												->where(function($query) {
															$query->where('lates.latetime_date', '>=', DB::raw('DATE_SUB(deployments.start_date, INTERVAL 11 MONTH)'))
																					->orWhere('attendances.status', 'Absent')
																					->where('attendances.attendance_date', '>=', DB::raw('DATE_SUB(deployments.start_date, INTERVAL 12 MONTH)'));
												})
												->groupBy('deployments.id') // Add this if you need to group by employee ID
												->havingRaw('COUNT(DISTINCT lates.id) >= 10')
												->havingRaw('COUNT(CASE WHEN attendances.status = "Absent" THEN 1 END) >= 10')
            ->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

			//total number of filtered data
			$totalFiltered = Deployment::select(
                            DB::raw('CONCAT(employees.last_name, ", ", employees.first_name, " ", employees.middle_name) AS full_name'), 'clients.name as company')
                            ->join('employees', 'deployments.employee_id', '=', 'employees.id')
                            ->join('clients', 'deployments.client_id', '=', 'clients.id')
                            ->where('status','new')
																												->where(function($query) {
																													$query->where('lates.latetime_date', '>=', DB::raw('DATE_SUB(deployments.start_date, INTERVAL 11 MONTH)'))
																																			->orWhere('attendances.status', 'Absent')
																																			->where('attendances.attendance_date', '>=', DB::raw('DATE_SUB(deployments.start_date, INTERVAL 12 MONTH)'));
																												})
																												->groupBy('deployments.id') // Add this if you need to group by employee ID
																												->havingRaw('COUNT(DISTINCT lates.id) >= 10')
																												->havingRaw('COUNT(CASE WHEN attendances.status = "Absent" THEN 1 END) >= 10')
																												->count();

		} else {
			$search = $request->input('search.value');

            $posts = Deployment::select(
                    DB::raw('CONCAT(employees.last_name, ", ", employees.first_name, " ", employees.middle_name) AS full_name'), 'clients.name as company')
                    ->join('employees', 'deployments.employee_id', '=', 'employees.id')
                    ->join('clients', 'deployments.client_id', '=', 'clients.id')
                    ->orWhere('employees.first_name', 'like', "%{$search}%")
                    ->orWhere('employees.middle_name', 'like', "%{$search}%")
                    ->orWhere('employees.last_name', 'like', "%{$search}%")
                    ->orWhere('clients.name', 'like', "%{$search}%")
																				->where(function($query) {
																					$query->where('lates.latetime_date', '>=', DB::raw('DATE_SUB(deployments.start_date, INTERVAL 11 MONTH)'))
																											->orWhere('attendances.status', 'Absent')
																											->where('attendances.attendance_date', '>=', DB::raw('DATE_SUB(deployments.start_date, INTERVAL 12 MONTH)'));
																				})
																				->groupBy('deployments.id') // Add this if you need to group by employee ID
																				->havingRaw('COUNT(DISTINCT lates.id) >= 10')
																				->havingRaw('COUNT(CASE WHEN attendances.status = "Absent" THEN 1 END) >= 10')
                    ->where('status','new')
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();

			//total number of filtered data matching the search value request in the Supplier table	
			$totalFiltered = Deployment::select(
                            DB::raw('CONCAT(employees.last_name, ", ", employees.first_name, " ", employees.middle_name) AS full_name'), 'clients.name as company')
                            ->join('employees', 'deployments.employee_id', '=', 'employees.id')
                            ->join('clients', 'deployments.client_id', '=', 'clients.id')
                            ->orWhere('employees.first_name', 'like', "%{$search}%")
                            ->orWhere('employees.middle_name', 'like', "%{$search}%")
                            ->orWhere('employees.last_name', 'like', "%{$search}%")
                            ->orWhere('clients.name', 'like', "%{$search}%")
																												->where('status','new')
																												->where(function($query) {
																													$query->where('lates.latetime_date', '>=', DB::raw('DATE_SUB(deployments.start_date, INTERVAL 11 MONTH)'))
																																			->orWhere('attendances.status', 'Absent')
																																			->where('attendances.attendance_date', '>=', DB::raw('DATE_SUB(deployments.start_date, INTERVAL 12 MONTH)'));
																												})
																												->groupBy('deployments.id') // Add this if you need to group by employee ID
																												->havingRaw('COUNT(DISTINCT lates.id) >= 10')
																												->havingRaw('COUNT(CASE WHEN attendances.status = "Absent" THEN 1 END) >= 10')
                            ->count();
		}


		$data = array();

		if ($posts) {
			//loop posts collection to transfer in another array $nestedData
			foreach ($posts as $r) {
				$nestedData['fullname'] = $r->full_name;
				$nestedData['client_name'] = $r->company;
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
