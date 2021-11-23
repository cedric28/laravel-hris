<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Supplier;
use App\Product;
use App\Inventory;
use App\Sale;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $now = Carbon::now();
        $yearNow =  $now->year;

        $sales = new Sale();
        $salesPerYear = $sales->selectRaw('year(created_at) as year, SUM(total_amount_due) as total_sales')
                            ->groupBy('year')
                            ->orderBy('year', 'asc')
                            ->get();
        $sales = $sales->whereYear('created_at','>=', $yearNow)->whereYear('created_at','<=', $yearNow);
        $totalAmountDue = $sales->sum('total_amount_due');

        $product = Inventory::count();
        $user = User::count();
        $supplier = Supplier::count();
        $salesPerMonth = $sales->selectRaw('month(created_at) as month, SUM(total_amount_due) as total_sales')
                ->groupBy('month')
                ->orderBy('month', 'asc')
                ->get();
       
        $this->authorize("isAdmin");
        return view('home',[
            "user" => $user,
            "product" => $product,
            "supplier" => $supplier,
            'totalAmountDue' => $totalAmountDue,
            'year' => $yearNow,
            'salesPerYear' => $salesPerYear,
            'salesPerMonth' => $salesPerMonth
        ]);
    }
}
