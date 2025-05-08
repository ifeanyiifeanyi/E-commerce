<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\CurrencyService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CurrencyMiddleware
{
    protected $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only detect currency if not already set in session
        if (!session()->has('currency')) {
            $this->currencyService->getUserCurrency();
        }

        return $next($request);
    }
}
