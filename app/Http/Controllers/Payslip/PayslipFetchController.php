<?php

namespace App\Http\Controllers\Payslip;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Payslip;

class PayslipFetchController extends Controller
{
  public function fetchPayslip(Request $request)
		{
			//column list in the table Prpducts
			$columns = array(
				0 => 'start_date',
				1 => 'end_date',
				2 => 'action'
			);

			$deployment_id = $request->deployment_id;
	
			//get the total number of data in User table
			$totalData = Payslip::select('payrolls.start_date','payrolls.end_date','payslips.id as id','payrolls.id as payrollId', 'payslips.other_deduction','payslips.other_pay', 'payslips.include_thirteen_month_pay')
			->join('payrolls', 'payslips.payroll_id', '=', 'payrolls.id')->where('payslips.deployment_id', $deployment_id)->count();
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
				$posts = Payslip::select('payrolls.start_date','payrolls.end_date','payslips.id as id', 'payrolls.id as payrollId', 'payslips.other_deduction','payslips.other_pay', 'payslips.include_thirteen_month_pay')
					->join('payrolls', 'payslips.payroll_id', '=', 'payrolls.id')
					->where('payslips.deployment_id', $deployment_id)
					->offset($start)
					->limit($limit)
					->orderBy($order, $dir)
					->get();
	
				//total number of filtered data
				$totalFiltered = Payslip::select('payrolls.start_date','payrolls.end_date','payslips.id as id','payrolls.id as payrollId', 'payslips.other_deduction','payslips.other_pay', 'payslips.include_thirteen_month_pay')
				->join('payrolls', 'payslips.payroll_id', '=', 'payrolls.id')->where('payslips.deployment_id', $deployment_id)->count();
			} else {
				$search = $request->input('search.value');
	
				$posts = Payslip::select('payrolls.start_date','payrolls.end_date','payslips.id as id','payrolls.id as payrollId', 'payslips.other_deduction','payslips.other_pay', 'payslips.include_thirteen_month_pay')
					->join('payrolls', 'payslips.payroll_id', '=', 'payrolls.id')
					->orWhere(function ($query) use ($search) {
						$query->whereHas('payroll', function ($query) use ($search) {
							$query->where('start_date', 'like', "%{$search}%")
							->orWhere('end_date', 'like', "%{$search}%");
						});
					})
					->where('payslips.deployment_id', $deployment_id)
					->offset($start)
					->limit($limit)
					->orderBy($order, $dir)
					->get();
	
				//total number of filtered data matching the search value request in the Supplier table	
				$totalFiltered = Payslip::select('payrolls.start_date','payrolls.end_date','payslips.id as id','payrolls.id as payrollId', 'payslips.other_deduction','payslips.other_pay', 'payslips.include_thirteen_month_pay')
					->join('payrolls', 'payslips.payroll_id', '=', 'payrolls.id')
					->orWhere(function ($query) use ($search) {
						$query->whereHas('payroll', function ($query) use ($search) {
							$query->where('start_date', 'like', "%{$search}%")
							->orWhere('end_date', 'like', "%{$search}%");
						});
					})
					->where('payslips.deployment_id', $deployment_id)
					->count();
			}
	
	
			$data = array();
	
			if ($posts) {
				//loop posts collection to transfer in another array $nestedData
				foreach ($posts as $r) {
					$nestedData['description'] = "Payroll Cut-Off ". date('M d, Y', strtotime($r->start_date)) ." - ".date('M d, Y', strtotime($r->end_date));
						$nestedData['action'] = '
						<button name="work-details" title="generate payslip" id="generate-payslip" data-id="' . $r->id . '" class="btn bg-gradient-primary btn-sm">Generate Payslip</button>
							<button name="edit" title="edit" id="edit_payslip" data-id="' . $r->id . '" data-payrollId="' . $r->payrollId.'" 
							data-otherDeduction="' . $r->other_deduction.'"
							data-otherPay="' . $r->other_pay.'"
							data-includeThirteenMonthPay="' . $r->include_thirteen_month_pay.'"
							class="btn bg-gradient-warning btn-sm"><i class="fas fa-pencil-alt"></i> Edit Payslip</button>
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
