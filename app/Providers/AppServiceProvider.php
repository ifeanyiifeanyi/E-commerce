<?php

namespace App\Providers;

use App\Channels\TwilioChannel;

use Illuminate\Pagination\Paginator;
use App\Channels\AfricasTalkingChannel;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Notification;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();
        Notification::extend('twilio', function ($app) {
            return new TwilioChannel();
        });

    }
}
