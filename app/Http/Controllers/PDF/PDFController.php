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

        view()->share('sales', $sales);
        $pdf = \PDF::loadView('pdf.yearly_sales', [
            'sales' => $sales,
            'totalAmountDue' => $totalAmountDue,
            'totalDiscount' => $totalDiscount,
            'totalPrice' => $totalPrice,
            'totalCashChange' => $totalCashChange,
            'totalCashTendered' => $totalCashTendered
        ]);

        return $pdf->download("Yearly-Sales-" . Carbon::now()->format('m-d-Y') . ".pdf");
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

        view()->share('sales', $sales);
        $pdf = \PDF::loadView('pdf.monthly_sales', [
            'sales' => $sales,
            'totalAmountDue' => $totalAmountDue,
            'totalDiscount' => $totalDiscount,
            'totalPrice' => $totalPrice,
            'totalCashChange' => $totalCashChange
        ]);

        return $pdf->download("Monthly-Sales-" . Carbon::now()->format('m-d-Y') . ".pdf");
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

        view()->share('deliveries', $deliveries);
        $pdf = \PDF::loadView('pdf.stock_medical_goods', $deliveries);

        return $pdf->download("Stocks-Medical-Goods-" . Carbon::now()->format('m-d-Y') . ".pdf");
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

        $deliveries = $deliveries->where('status', 'pending')->oldest()->get();
        view()->share('deliveries', $deliveries);

        $pdf = \PDF::loadView('pdf.delivery_schedule', $deliveries);

        return $pdf->download("Delivery-Schedule-" . Carbon::now()->format('m-d-Y') . ".pdf");
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

        view()->share('customerPoint', $customerPoint);
        $pdf = \PDF::loadView('pdf.customer_discounts', $customerPoint);

        return $pdf->download("Customer-Discount-" . Carbon::now()->format('m-d-Y') . ".pdf");
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

        view()->share('deliveries', $deliveries);
        $pdf = \PDF::loadView('pdf.daily_preventive', $deliveries);

        return $pdf->download("Daily-Preventive-" . Carbon::now()->format('m-d-Y') . ".pdf");
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

        view()->share('returnStocks', $returnStocks);
        $pdf = \PDF::loadView('pdf.return_products', $returnStocks);

        return $pdf->download("Return-Products-Report-" . Carbon::now()->format('m-d-Y') . ".pdf");
    }
}
