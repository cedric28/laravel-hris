<?php

namespace App\Http\Controllers\PDF;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Sale;
use App\Stock;
use App\DeliveryRequest;
use App\ReturnStockItem;
use App\DeliveryRequestItem;
use App\Customer;
use Carbon\Carbon;
use PDF;

class PDFController extends Controller
{
    public function generateInvoice(Request $request, $id)
    {
        $sales = Sale::withTrashed()->findOrFail($id);
        // return view("pdf.invoice",[
        //     'sales' => $sales
        // ]);
        // share data to view
        view()->share('sales', $sales);
        $pdf = PDF::loadView('pdf.invoice', $sales);

        return $pdf->stream("sales_invoice_" . $sales->or_no . ".pdf");
    }

    public function generateSalesYearly(Request $request)
    {
        $sales = new Sale();

        if ($request->start_date) {
            $sales = $sales->whereYear('created_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $sales = $sales->whereYear('created_at', '<=', $request->end_date);
        }

        $sales = $sales->latest()->get();

        $totalAmountDue = $sales->sum('total_amount_due');
        $totalDiscount = $sales->sum('total_discount');
        $totalPrice = $sales->sum('total_price');
        $totalCashChange = $sales->sum('cash_change');
        $totalCashTendered = $sales->sum('cash_tendered');

        //check current user
        $user = \Auth::user();
        $fullName = $user->last_name . ", " . $user->first_name;

        $dateToday = Carbon::now()->format('m/d/Y g:ia');

        view()->share('sales', $sales);
        $pdf = \PDF::loadView('pdf.yearly_sales', [
            'sales' => $sales,
            'totalAmountDue' => $totalAmountDue,
            'totalDiscount' => $totalDiscount,
            'totalPrice' => $totalPrice,
            'totalCashChange' => $totalCashChange,
            'totalCashTendered' => $totalCashTendered,
            "dateToday" => $dateToday,
            'fullName' => $fullName
        ]);

        return $pdf->download("Yearly-Sales-" . Carbon::now()->format('m-d-Y') . ".pdf");
    }

    public function printSalesYearly(Request $request)
    {
        $sales = new Sale();

        if ($request->start_date) {
            $sales = $sales->whereYear('created_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $sales = $sales->whereYear('created_at', '<=', $request->end_date);
        }

        $sales = $sales->latest()->get();

        $totalAmountDue = $sales->sum('total_amount_due');
        $totalDiscount = $sales->sum('total_discount');
        $totalPrice = $sales->sum('total_price');
        $totalCashChange = $sales->sum('cash_change');
        $totalCashTendered = $sales->sum('cash_tendered');

        //check current user
        $user = \Auth::user();
        $fullName = $user->last_name . ", " . $user->first_name;

        $dateToday = Carbon::now()->format('m/d/Y g:ia');

        return view('pdf.yearly_sales', [
            'sales' => $sales,
            'totalAmountDue' => $totalAmountDue,
            'totalDiscount' => $totalDiscount,
            'totalPrice' => $totalPrice,
            'totalCashChange' => $totalCashChange,
            'totalCashTendered' => $totalCashTendered,
            "dateToday" => $dateToday,
            'fullName' => $fullName
        ]);
    }

    public function generateSalesMonthly(Request $request)
    {
        $now = Carbon::now();
        $yearNow =  $now->year;

        $sales = new Sale();

        if ($request->start_date) {
            $sales = $sales->whereMonth('created_at', '>=', '11')->whereYear('created_at', '>=', $yearNow);
        } else {
            $sales = $sales->whereYear('created_at', '>=', $yearNow);
        }

        if ($request->end_date) {
            $sales = $sales->whereMonth('created_at', '<=', '11')->whereYear('created_at', '<=', $yearNow);
        } else {
            $sales = $sales->whereYear('created_at', '<=', $yearNow);
        }

        $sales = $sales->latest()->get();

        $totalAmountDue = $sales->sum('total_amount_due');
        $totalDiscount = $sales->sum('total_discount');
        $totalPrice = $sales->sum('total_price');
        $totalCashChange = $sales->sum('cash_change');

        //check current user
        $user = \Auth::user();
        $fullName = $user->last_name . ", " . $user->first_name;

        $dateToday = Carbon::now()->format('m/d/Y g:ia');

        view()->share('sales', $sales);
        $pdf = \PDF::loadView('pdf.monthly_sales', [
            'sales' => $sales,
            'totalAmountDue' => $totalAmountDue,
            'totalDiscount' => $totalDiscount,
            'totalPrice' => $totalPrice,
            'totalCashChange' => $totalCashChange,
            "dateToday" => $dateToday,
            'fullName' => $fullName
        ]);

        return $pdf->download("Monthly-Sales-" . Carbon::now()->format('m-d-Y') . ".pdf");
    }

    public function printSalesMonthly(Request $request)
    {
        $now = Carbon::now();
        $yearNow =  $now->year;

        $sales = new Sale();

        if ($request->start_date) {
            $sales = $sales->whereMonth('created_at', '>=', '11')->whereYear('created_at', '>=', $yearNow);
        } else {
            $sales = $sales->whereYear('created_at', '>=', $yearNow);
        }

        if ($request->end_date) {
            $sales = $sales->whereMonth('created_at', '<=', '11')->whereYear('created_at', '<=', $yearNow);
        } else {
            $sales = $sales->whereYear('created_at', '<=', $yearNow);
        }

        $sales = $sales->latest()->get();

        $totalAmountDue = $sales->sum('total_amount_due');
        $totalDiscount = $sales->sum('total_discount');
        $totalPrice = $sales->sum('total_price');
        $totalCashChange = $sales->sum('cash_change');
        //check current user
        $user = \Auth::user();
        $fullName = $user->last_name . ", " . $user->first_name;

        $dateToday = Carbon::now()->format('m/d/Y g:ia');

        return view('pdf.monthly_sales', [
            'sales' => $sales,
            'totalAmountDue' => $totalAmountDue,
            'totalDiscount' => $totalDiscount,
            'totalPrice' => $totalPrice,
            'totalCashChange' => $totalCashChange,
            "dateToday" => $dateToday,
            'fullName' => $fullName
        ]);
    }

    public function generateStockMedicalGoods(Request $request)
    {

        $deliveries = new DeliveryRequestItem();

        if ($request->start_date) {
            $search = $request->start_date;

            $deliveries = $deliveries->with('product')->whereHas("delivery_request", function ($q) use ($search) {
                $q->whereDate('delivery_at', '>=', Carbon::parse($search)->format('Y-m-d'));
            });
        }

        if ($request->end_date) {
            $search = $request->end_date;

            $deliveries = $deliveries->with('product')->whereHas("delivery_request", function ($q) use ($search) {
                $q->whereDate('delivery_at', '<=', Carbon::parse($search)->format('Y-m-d'));
            });
        }

        $deliveries = $deliveries->whereHas("delivery_request", function ($q) {
            $q->where("status", "=", "completed");
        });

        $deliveries = $deliveries->latest()->get();

        //check current user
        $user = \Auth::user();
        $fullName = $user->last_name . ", " . $user->first_name;

        $dateToday = Carbon::now()->format('m/d/Y g:ia');

        view()->share('deliveries', [
            "deliveries" => $deliveries,
            "dateToday" => $dateToday,
            'fullName' => $fullName
        ]);
        $pdf = \PDF::loadView('pdf.stock_medical_goods', [
            "deliveries" => $deliveries,
            "dateToday" => $dateToday,
            'fullName' => $fullName
        ]);

        return $pdf->download("Stocks-Medical-Goods-" . Carbon::now()->format('m-d-Y') . ".pdf");
    }

    public function printStockMedicalGoods(Request $request)
    {

        $deliveries = new DeliveryRequestItem();

        if ($request->start_date) {
            $search = $request->start_date;

            $deliveries = $deliveries->with('product')->whereHas("delivery_request", function ($q) use ($search) {
                $q->whereDate('delivery_at', '>=', Carbon::parse($search)->format('Y-m-d'));
            });
        }

        if ($request->end_date) {
            $search = $request->end_date;

            $deliveries = $deliveries->with('product')->whereHas("delivery_request", function ($q) use ($search) {
                $q->whereDate('delivery_at', '<=', Carbon::parse($search)->format('Y-m-d'));
            });
        }

        $deliveries = $deliveries->whereHas("delivery_request", function ($q) {
            $q->where("status", "=", "completed");
        });

        $deliveries = $deliveries->latest()->get();

        //check current user
        $user = \Auth::user();
        $fullName = $user->last_name . ", " . $user->first_name;

        $dateToday = Carbon::now()->format('m/d/Y g:ia');

        return view('pdf.stock_medical_goods', [
            "deliveries" => $deliveries,
            "dateToday" => $dateToday,
            'fullName' => $fullName
        ]);
    }

    public function generateDeliverySchedule(Request $request)
    {
        $deliveries = new DeliveryRequest();
        $deliveries = $deliveries->orderBy('supplier_id', 'asc');

        if ($request->start_date) {
            $deliveries = $deliveries->with('supplier')->whereDate('delivery_at', '>=', Carbon::parse($request->start_date)->format('Y-m-d'));
        }
        if ($request->end_date) {
            $deliveries = $deliveries->with('supplier')->whereDate('delivery_at', '<=', Carbon::parse($request->end_date)->format('Y-m-d'));
        }

        $deliveries = $deliveries->orderBy("status", 'desc')->oldest()->get();

        //check current user
        $user = \Auth::user();
        $fullName = $user->last_name . ", " . $user->first_name;

        $dateToday = Carbon::now()->format('m/d/Y g:ia');

        view()->share('deliveries', [
            "deliveries" => $deliveries,
            "dateToday" => $dateToday,
            'fullName' => $fullName
        ]);

        $pdf = \PDF::loadView('pdf.delivery_schedule', [
            "deliveries" => $deliveries,
            "dateToday" => $dateToday,
            'fullName' => $fullName
        ]);

        return $pdf->download("Delivery-Schedule-" . Carbon::now()->format('m-d-Y') . ".pdf");
    }

    public function printDeliverySchedule(Request $request)
    {
        $deliveries = new DeliveryRequest();
        $deliveries = $deliveries->orderBy('supplier_id', 'asc');

        if ($request->start_date) {
            $deliveries = $deliveries->with('supplier')->whereDate('delivery_at', '>=', Carbon::parse($request->start_date)->format('Y-m-d'));
        }
        if ($request->end_date) {
            $deliveries = $deliveries->with('supplier')->whereDate('delivery_at', '<=', Carbon::parse($request->end_date)->format('Y-m-d'));
        }

        $deliveries = $deliveries->where('status', 'pending')->oldest()->get();

        //check current user
        $user = \Auth::user();
        $fullName = $user->last_name . ", " . $user->first_name;

        $dateToday = Carbon::now()->format('m/d/Y g:ia');

        return view("pdf.delivery_schedule", [
            'deliveries' => $deliveries,
            "dateToday" => $dateToday,
            'fullName' => $fullName
        ]);
    }

    public function generateCustomerDiscount(Request $request)
    {
        $customerPoint = new Customer();

        if ($request->start_date) {
            $customerPoint = $customerPoint->whereDate('created_at', '>=', Carbon::parse($request->start_date)->format('Y-m-d'));
        }
        if ($request->end_date) {
            $customerPoint = $customerPoint->whereDate('created_at', '<=', Carbon::parse($request->end_date)->format('Y-m-d'));
        }

        $customerPoint = $customerPoint->latest()->get();
        //check current user
        $user = \Auth::user();
        $fullName = $user->last_name . ", " . $user->first_name;

        $dateToday = Carbon::now()->format('m/d/Y g:ia');

        view()->share('customerPoint', [
            "customerPoint" => $customerPoint,
            "dateToday" => $dateToday,
            'fullName' => $fullName
        ]);
        $pdf = \PDF::loadView('pdf.customer_discounts', [
            "customerPoint" => $customerPoint,
            "dateToday" => $dateToday,
            'fullName' => $fullName
        ]);

        return $pdf->download("Customer-Discount-" . Carbon::now()->format('m-d-Y') . ".pdf");
    }

    public function printCustomerDiscount(Request $request)
    {
        $customerPoint = new Customer();

        if ($request->start_date) {
            $customerPoint = $customerPoint->whereDate('created_at', '>=', Carbon::parse($request->start_date)->format('Y-m-d'));
        }
        if ($request->end_date) {
            $customerPoint = $customerPoint->whereDate('created_at', '<=', Carbon::parse($request->end_date)->format('Y-m-d'));
        }

        //check current user
        $user = \Auth::user();
        $fullName = $user->last_name . ", " . $user->first_name;

        $customerPoint = $customerPoint->latest()->get();

        $dateToday = Carbon::now()->format('m/d/Y g:ia');

        return view("pdf.customer_discounts", [
            "customerPoint" => $customerPoint,
            "dateToday" => $dateToday,
            'fullName' => $fullName
        ]);
    }

    public function generateDailyPreventive(Request $request)
    {
        $deliveries = new DeliveryRequestItem();
        $deliveries = $deliveries->orderBy('expired_at', 'asc');

        if ($request->start_date) {
            $search = $request->start_date;

            $deliveries = $deliveries->with('product')->whereDate('expired_at', '>=', Carbon::parse($search)->format('Y-m-d'));
        }

        if ($request->end_date) {
            $search = $request->end_date;

            $deliveries = $deliveries->with('product')->whereDate('expired_at', '<=', Carbon::parse($search)->format('Y-m-d'));
        }

        $deliveries->whereHas("delivery_request", function ($q) {
            $q->where('status', '=', 'completed');
        });

        $deliveries = $deliveries->get();

        //check current user
        $user = \Auth::user();
        $fullName = $user->last_name . ", " . $user->first_name;

        $dateToday = Carbon::now()->format('m/d/Y g:ia');

        view()->share('deliveries', [
            "deliveries" => $deliveries,
            "dateToday" => $dateToday,
            'fullName' => $fullName
        ]);
        $pdf = \PDF::loadView('pdf.daily_preventive', [
            "deliveries" => $deliveries,
            "dateToday" => $dateToday,
            'fullName' => $fullName
        ]);

        return $pdf->download("Daily-Preventive-" . Carbon::now()->format('m-d-Y') . ".pdf");
    }

    public function printDailyPreventive(Request $request)
    {
        $deliveries = new DeliveryRequestItem();
        $deliveries = $deliveries->orderBy('expired_at', 'asc');

        if ($request->start_date) {
            $search = $request->start_date;

            $deliveries = $deliveries->with('product')->whereDate('expired_at', '>=', Carbon::parse($search)->format('Y-m-d'));
        }

        if ($request->end_date) {
            $search = $request->end_date;

            $deliveries = $deliveries->with('product')->whereDate('expired_at', '<=', Carbon::parse($search)->format('Y-m-d'));
        }

        $deliveries->whereHas("delivery_request", function ($q) {
            $q->where('status', '=', 'completed');
        });

        $deliveries = $deliveries->get();

        //check current user
        $user = \Auth::user();
        $fullName = $user->last_name . ", " . $user->first_name;

        $dateToday = Carbon::now()->format('m/d/Y g:ia');

        return view('pdf.daily_preventive', [
            "deliveries" => $deliveries,
            "dateToday" => $dateToday,
            'fullName' => $fullName
        ]);
    }

    public function generateReturnStocks(Request $request)
    {
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

        $returnStocks = $returnStocks->join('return_stocks', 'return_stock_items.return_stock_id', '=', 'return_stocks.id')->orderBy('return_stocks.supplier_id', 'asc')->get();

        //check current user
        $user = \Auth::user();
        $fullName = $user->last_name . ", " . $user->first_name;

        $dateToday = Carbon::now()->format('m/d/Y g:ia');

        view()->share('returnStocks', [
            "returnStocks" => $returnStocks,
            "dateToday" => $dateToday,
            'fullName' => $fullName
        ]);
        $pdf = \PDF::loadView('pdf.return_products', [
            "returnStocks" => $returnStocks,
            "dateToday" => $dateToday,
            'fullName' => $fullName
        ]);

        return $pdf->download("Return-Products-Report-" . Carbon::now()->format('m-d-Y') . ".pdf");
    }

    public function printReturnStocks(Request $request)
    {
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

        $returnStocks = $returnStocks->join('return_stocks', 'return_stock_items.return_stock_id', '=', 'return_stocks.id')->orderBy('return_stocks.supplier_id', 'asc')->get();

        //check current user
        $user = \Auth::user();
        $fullName = $user->last_name . ", " . $user->first_name;

        $dateToday = Carbon::now()->format('m/d/Y g:ia');

        return view('pdf.return_products', [
            "returnStocks" => $returnStocks,
            "dateToday" => $dateToday,
            'fullName' => $fullName
        ]);
    }
}
