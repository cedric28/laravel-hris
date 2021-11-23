<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Customer;
use Carbon\Carbon;
use Validator;

class CustomerDiscountReportController extends Controller
{
    public function customerDiscount(Request $request)
    {
        $messages = [
            'lte' => 'The :attribute year must be less than or equal to end date.',
        ];

        //validate request value
        $validator = Validator::make($request->all(), [
            'start_date' => '',
            'end_date' => 'after_or_equal:start_date',
        ], $messages);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors())->withInput();
        }

        $customerPoint = new Customer();
      
        if($request->start_date) {
            $customerPoint = $customerPoint->whereDate('created_at', '>=', Carbon::parse($request->start_date)->format('Y-m-d'));
          
        }
        if($request->end_date) {
            $customerPoint = $customerPoint->whereDate('created_at', '<=', Carbon::parse($request->end_date)->format('Y-m-d'));
        }
        
        $customerPoint = $customerPoint->latest()->paginate(10);

        return view("reports.customer_discounts",[
            'customerPoint' => $customerPoint
        ]);
    }
}
