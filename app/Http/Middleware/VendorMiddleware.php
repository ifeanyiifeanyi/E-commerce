<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VendorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || Auth::user()->role !== 'vendor') {
            return redirect()->route('vendor.login.view')
                ->with('error', 'You must be logged in as a vendor to access this page.');
        }

        // Check if vendor is active
        if (Auth::user()->status === 'inactive') {
            return redirect()->route('vendor.pending');
        }

        return $next($request);
    }
}
