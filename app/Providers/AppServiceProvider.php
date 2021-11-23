<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use App\Stock;
use App\DeliveryRequest;
use Carbon\Carbon;
use Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $stocks = new Stock();
        $stockNearToExpire = $stocks->where('expired_at', '<=', Carbon::now()->addDays(7)->format('Y-m-d'))->count();

        $deliveries = new DeliveryRequest();
        $totalDeliveries = $deliveries->where('delivery_at', '<=', Carbon::now()->addDays(7)->format('Y-m-d'))->count();
        $totalNotification = $totalDeliveries + $stockNearToExpire;
        View::share([
            'expiredProducts' => $stockNearToExpire,
            'totalDeliveries' => $totalDeliveries,
            'totalNotification' => $totalNotification

        ]);
        Schema::defaultStringLength(191);
    }
}
