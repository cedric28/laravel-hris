<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Deployment;
use App\Employee;
use App\Client;
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

        $user = User::count();
        $totalEmployees = Deployment::where('status','new')->whereYear('start_date', $yearNow)->count();
        $totalApplicants = Employee::count();
        $totalClients = Client::count();

        $this->authorize("isAdmin");
        
        return view('home',[
            "user" => $user,
            "product" => [],
            "supplier" => [],
            'totalAmountDue' => [],
            'year' => $yearNow,
            'salesPerYear' => [],
            'salesPerMonth' => [],
            'totalEmployees' => $totalEmployees,
            'totalApplicants' => $totalApplicants,
            'totalClients' => $totalClients
        ]);
    }
}
