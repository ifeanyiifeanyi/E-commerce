<?php

namespace App\Providers;


use App\Models\User;
use App\Models\CustomerAddress;
use App\Services\CurrencyService;

use App\Observers\CustomerObserver;
use App\Models\CustomerLoginHistory;
use App\Models\CustomerNotification;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use App\Observers\CustomerAddressObserver;
use Illuminate\Support\Facades\Notification;
use App\Observers\CustomerLoginHistoryObserver;
use App\Observers\CustomerNotificationObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CurrencyService::class, function ($app) {
            return new CurrencyService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();

        // Register model observers
        CustomerLoginHistory::observe(CustomerLoginHistoryObserver::class);
        User::observe(CustomerObserver::class);
        CustomerAddress::observe(CustomerAddressObserver::class);
        CustomerNotification::observe(CustomerNotificationObserver::class);
    }
}
