<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DeliveryRequest;
use Validator;
use Carbon\Carbon;

class DeliveryScheduleController extends Controller
{
    public function deliverySchedule(Request $request)
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

        $deliveries = new DeliveryRequest();
        $deliveries = $deliveries->orderBy('supplier_id', 'asc');

        if ($request->start_date) {
            $deliveries = $deliveries->with('supplier')->whereDate('delivery_at', '>=', Carbon::parse($request->start_date)->format('Y-m-d'));
        }
        if ($request->end_date) {
            $deliveries = $deliveries->with('supplier')->whereDate('delivery_at', '<=', Carbon::parse($request->end_date)->format('Y-m-d'));
        }

        $deliveries = $deliveries->where('status', 'pending')->oldest()->paginate(10);

        return view("reports.delivery_schedule", [
            'deliveries' => $deliveries
        ]);
    }
}
