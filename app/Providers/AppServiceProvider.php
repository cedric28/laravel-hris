<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use App\DeliveryRequestItem;
use App\DeliveryRequest;
use App\NotificationSetting;
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
        $stocks = new DeliveryRequestItem();
        $stocks = $stocks->whereBetween(
            'expired_at',
            [
                Carbon::now()->format('Y-m-d'),
                Carbon::now()->addDays(7)->format('Y-m-d')
            ]
        )
            ->whereHas("delivery_request", function ($q) {
                $q->where("status", "=", "completed");
            })
            ->count();

        $deliveries = new DeliveryRequest();
        $totalDeliveries = $deliveries->whereBetween(
            'delivery_at',
            [
                Carbon::now()->format('Y-m-d'),
                Carbon::now()->addDays(7)->format('Y-m-d')
            ]
        )->where('status', 'pending')->count();

        $notificationSetUp = NotificationSetting::withTrashed()->findOrFail(1);
        $totalDeliveries = $notificationSetUp->deliver_schedule_notif > 0 ? 0 : $totalDeliveries;
        $stocks = $notificationSetUp->near_expiry_notif > 0 ? 0 : $stocks;
        $totalNotification = $totalDeliveries + $stocks;
        View::share([
            'expiredProducts' => $stocks,
            'totalDeliveries' => $totalDeliveries,
            'totalNotification' => $totalNotification

        ]);

        Schema::defaultStringLength(191);

        Str::macro('currency', function ($price) {
            return number_format($price, 2);
        });
    }
}
