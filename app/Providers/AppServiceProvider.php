<?php

namespace App\Providers;


use App\Services\CurrencyService;
use App\Models\CustomerLoginHistory;
use Illuminate\Pagination\Paginator;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Notification;
use App\Observers\CustomerLoginHistoryObserver;

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

        CustomerLoginHistory::observe(CustomerLoginHistoryObserver::class);
    }
}
