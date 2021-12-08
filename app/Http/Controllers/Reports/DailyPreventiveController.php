<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DeliveryRequestItem;
use Carbon\Carbon;
use Validator;

class DailyPreventiveController extends Controller
{
    public function dailyPreventive(Request $request)
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

        $deliveries = new DeliveryRequestItem();
        $deliveries = $deliveries->where('expired_at', '<=', Carbon::now()->addDays(7)->format('Y-m-d'));
        if($request->start_date) {
            $search = $request->start_date;
           
            $deliveries = $deliveries->with('product')->whereDate('expired_at', '>=', Carbon::parse($search)->format('Y-m-d'));
        }

        if($request->end_date) {
            $search = $request->end_date;
           
            $deliveries = $deliveries->with('product')->whereDate('expired_at', '<=', Carbon::parse($search)->format('Y-m-d'));
        }

        $deliveries->whereHas("delivery_request", function($q){
            $q->where('status', '=', 'completed');
        });

        $deliveries = $deliveries->latest()->paginate(10);

        return view("reports.daily_preventive",[
            'deliveries' => $deliveries
        ]);
    }
}
