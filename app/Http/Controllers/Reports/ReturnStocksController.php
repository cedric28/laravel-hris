<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ReturnStockItem;
use Carbon\Carbon;
use Validator;

class ReturnStocksController extends Controller
{
    public function returnStocks(Request $request)
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

        $returnStocks = new ReturnStockItem();

        if ($request->start_date) {
            $search = $request->start_date;

            $returnStocks = $returnStocks->with('product')->whereHas("return_stock", function ($q) use ($search) {
                $q->whereDate('delivery_at', '>=', Carbon::parse($search)->format('Y-m-d'));
            });
        }

        if ($request->end_date) {
            $search = $request->end_date;

            $returnStocks = $returnStocks->with('product')->whereHas("return_stock", function ($q) use ($search) {
                $q->whereDate('delivery_at', '<=', Carbon::parse($search)->format('Y-m-d'));
            });
        }

        $returnStocks = $returnStocks->join('return_stocks', 'return_stock_items.return_stock_id', '=', 'return_stocks.id')->orderBy('return_stocks.supplier_id', 'asc')->paginate(10);


        return view("reports.return_products", [
            'returnStocks' => $returnStocks
        ]);
    }
}
