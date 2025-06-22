<?php

namespace App\Providers;

use App\Models\Product;
use App\Models\MeasurementUnit;
use Illuminate\Auth\Events\Login;
use App\Observers\ProductObserver;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use App\Listeners\UserLoggedInListener;
use App\Observers\MeasurementUnitObserver;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        Login::class => [
            UserLoggedInListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        MeasurementUnit::observe(MeasurementUnitObserver::class);
        Product::observe(ProductObserver::class);
    }
}
