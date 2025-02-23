<?php

namespace App\Http\Controllers;

use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        switch ($request->user()->role) {
            case 'admin':
                return redirect(route('admin.dashboard', absolute: false));
                break;
            case 'user':
                return redirect(route('user.dashboard', absolute: false));
                break;
            case 'vendor':
                return redirect(route('vendor.dashboard', absolute: false));
                break;
            default:
                Request::logout();
                return redirect(route('login', absolute: false));
                break;
        }
    }
}
