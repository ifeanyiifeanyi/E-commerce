<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\LoginHistoryService;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Auth\LoginRequest;

class AuthenticatedSessionController extends Controller
{

     protected LoginHistoryService $loginHistoryService;

    public function __construct(LoginHistoryService $loginHistoryService)
    {
        $this->loginHistoryService = $loginHistoryService;
    }
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
       try {
            // Attempt authentication
            $request->authenticate();

            $request->session()->regenerate();

            // Log successful login
            $user = Auth::user();
            $this->loginHistoryService->logLoginAttempt($user, $request, true);

            return redirect()->intended(route('dashboard', absolute: false));
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Get user by email for failed attempt logging
            $user = User::where('email', $request->email)->first();

            // Log failed login attempt
            $this->loginHistoryService->logLoginAttempt(
                $user,
                $request,
                false,
                'Invalid credentials'
            );

            // Re-throw the exception to maintain default error handling
            throw $e;
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
