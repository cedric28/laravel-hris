<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Sale;
use Carbon\Carbon;
use Validator;

class SalesReportController extends Controller
{
    public function salesYearly(Request $request)
    {
            $messages = [
                'lte' => 'The :attribute year must be less than or equal to end date.',
            ];
            //validate request value
            $validator = Validator::make($request->all(), [
                'end_date' => '',
                'start_date' => 'lte:end_date',
           ], $messages);
   
            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }
            
            $sales = new Sale();
        
            if($request->start_date) {
                $sales = $sales->whereYear('created_at', '>=', $request->start_date);
            }
            if($request->end_date) {
                $sales = $sales->whereYear('created_at', '<=', $request->end_date);
            }
        
            $sales = $sales->latest()->paginate(10);

            $totalAmountDue = $sales->sum('total_amount_due');
            $totalDiscount = $sales->sum('total_discount');
            $totalPrice = $sales->sum('total_price');
        
            return view("reports.sales_yearly",[
                'sales' => $sales,
                'totalAmountDue' => $totalAmountDue,
                'totalDiscount' => $totalDiscount,
                'totalPrice' => $totalPrice
            ]);
        
    }

    public function salesMonthly(Request $request)
    {
            $messages = [
                'lte' => 'The :attribute year must be less than end date.',
            ];
            //validate request value
            $validator = Validator::make($request->all(), [
                'end_date' => '',
                'start_date' => 'lte:end_date',
           ], $messages);
   
            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }
            
            $now = Carbon::now();
            $yearNow =  $now->year;

            $sales = new Sale();
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            if($startDate) {
                $sales = $sales->whereMonth('created_at', '>=', $startDate)->whereYear('created_at','>=', $yearNow);
            } else {
                $sales = $sales->whereYear('created_at','>=', $yearNow);
            }

            if($endDate) {
                $sales = $sales->whereMonth('created_at', '<=', $endDate)->whereYear('created_at','<=', $yearNow);
            } else {
                $sales = $sales->whereYear('created_at','<=', $yearNow);
            }
        
            $sales = $sales->oldest()->paginate(10);

            $totalAmountDue = $sales->sum('total_amount_due');
            $totalDiscount = $sales->sum('total_discount');
            $totalPrice = $sales->sum('total_price');
        
            return view("reports.sales_monthly",[
                'sales' => $sales,
                'totalAmountDue' => $totalAmountDue,
                'totalDiscount' => $totalDiscount,
                'totalPrice' => $totalPrice
            ]);
    }
}
