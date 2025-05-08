<?php

use Monolog\Handler\RollbarHandler;
use Illuminate\Foundation\Application;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\VendorMiddleware;
use App\Http\Middleware\CurrencyMiddleware;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->alias(
            [
                'role' => RoleMiddleware::class,
                'vendor.member' => VendorMiddleware::class,
                'currency' => CurrencyMiddleware::class,
            ]

        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
