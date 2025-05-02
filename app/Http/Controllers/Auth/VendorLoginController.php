<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
class VendorLoginController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        // If already logged in as vendor, redirect to vendor dashboard
        if (Auth::check() && Auth::user()->isVendor()) {
            // If vendor is inactive, redirect to pending approval page
            if (Auth::user()->status === 'inactive') {
                return redirect()->route('vendor.pending');
            }

            return redirect()->route('vendor.dashboard');
        }

        return view('auth.vendor-login');
    }

    /**
     * Handle the login request
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }

        // Determine login field type (email, phone, or username)
        $loginField = $this->determineLoginField($request->username);
        $credentials = [
            $loginField => $request->username,
            'password' => $request->password,
        ];

        // Attempt to login with the provided credentials
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Check if the authenticated user is a vendor
            if ($user->role !== 'vendor') {
                Auth::logout();
                return redirect()->back()
                    ->withErrors(['username' => 'These credentials do not match a vendor account.'])
                    ->withInput($request->except('password'));
            }

            $request->session()->regenerate();

            // Check if vendor is inactive (pending approval)
            if ($user->status === 'inactive') {
                return redirect()->route('vendor.pending');
            }

            return redirect()->intended(route('vendor.dashboard'));
        }

        // If authentication failed
        return redirect()->back()
            ->withErrors(['username' => 'The provided credentials do not match our records.'])
            ->withInput($request->except('password'));
    }

    /**
     * Determine what field to use for login (email, phone, or username)
     *
     * @param string $input
     * @return string
     */
    private function determineLoginField($input)
    {
        // Check if input is an email
        if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
            return 'email';
        }

        // Check if input is a phone number (basic validation)
        if (preg_match('/^[0-9]{10,15}$/', preg_replace('/[^0-9]/', '', $input))) {
            return 'phone';
        }

        // Default to username
        return 'username';
    }

    /**
     * Log the vendor out
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('vendor.login.view');
    }
}
