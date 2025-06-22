<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CaptureLocationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Store location data in session if provided
        if ($request->has('latitude') && $request->has('longitude')) {
            session([
                'user_latitude' => $request->input('latitude'),
                'user_longitude' => $request->input('longitude'),
            ]);
        }

        // Store referral source in session
        if ($request->has('ref')) {
            session(['referral_source' => $request->input('ref')]);
        }

        return $next($request);
    }
}
