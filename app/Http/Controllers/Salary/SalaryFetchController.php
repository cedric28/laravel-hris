<?php

namespace App\Http\Controllers\Salary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Deployment;
use Carbon\Carbon;

class SalaryFetchController extends Controller
{
    public function fetchCompensation(Request $request)
	{
		//column list in the table Prpducts
		$columns = array(
			0 => 'start_date',
			1 => 'action'
		);

  $deployment_id = $request->deployment_id;

		// Total hours worked
		$currentMonth = date('m');
		$currentYear = date('Y');
		//total number of data that will show in the datatable default 10
		$limit = $request->input('length');
		//start number for pagination ,default 0
		$start = $request->input('start');
		//order list of the column
		$order = $columns[$request->input('order.0.column')];
		//order by ,default asc 
		$dir = $request->input('order.0.dir');

	//get all the User data
		$employee =  Deployment::where('id',$deployment_id)->first();

	
		$startDate = Carbon::parse($employee->start_date);
		$currentDate = now();

		$payrollPeriods = collect();

		while ($startDate <= $currentDate) {
						$month = $startDate->month;
						$year = $startDate->year;

						if ($startDate->day <= 15) {
										$payrollPeriod = Carbon::create($year, $month, 1)->format('m/d/Y') . ' - ' . Carbon::create($year, $month, 15)->format('m/d/Y');
										$payrollPeriods->push($payrollPeriod);
										$startDate = Carbon::create($year, $month, 16);
						} else {
										$payrollPeriod = Carbon::create($year, $month, 16)->format('m/d/Y') . ' - ' . Carbon::create($year, $month)->endOfMonth()->format('m/d/Y');
									 $payrollPeriods->push($payrollPeriod);
										$startDate = Carbon::create($year, $month)->startOfMonth()->addMonth();
						}
		}
		$totalData = $payrollPeriods->count();
		$totalFiltered = $payrollPeriods->count();

		$payrollPeriods = $payrollPeriods->sortByDesc(function($item) {
			return strtotime(explode(" - ", $item)[0]);
});

		$payrollPeriods = $payrollPeriods->slice($start, $limit);

						
		$data = array();

		if ($payrollPeriods) {
			//loop posts collection to transfer in another array $nestedData
	
			
			foreach ($payrollPeriods as $r) {
				$parts = explode(" - ", $r);
				$start_date = $parts[0];
				$end_date = $parts[1];
				$nestedData['description'] = "Payroll for ".$r;
				$nestedData['action'] = '
						<button name="delete" id="generate-payslip" title="generate payslip" data-startdate="' . $start_date . '" data-enddate="' . $end_date . '" class="btn bg-gradient-info btn-sm">Generate Payslip</button>
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

	public function getStartDate($dateRange) {
			$parts = explode(" - ", $dateRange);
			return strtotime($parts[0]);
	}
}
