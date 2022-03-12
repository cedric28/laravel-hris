<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Inventory;
use App\InventoryLevel;
use Carbon\Carbon;
use Validator;

class OrderController extends Controller
{
    public function orderReport(Request $request)
    {
        $inventoryLevel = InventoryLevel::all();
        $reStock = $inventoryLevel[0]->re_stock;
        $critical = $inventoryLevel[0]->critical;
        $orderReports = Inventory::where(function ($query) use ($reStock, $critical) {
            $query->where("quantity", "=", 0)
                ->orWhere("quantity", "<", $reStock)
                ->orWhere("quantity", "=", $critical);
        });

        $orderReports = $orderReports->paginate(10);

        return view("reports.order", [
            'orderReports' => $orderReports,
            'inventoryLevel' => $inventoryLevel
        ]);
    }
}
