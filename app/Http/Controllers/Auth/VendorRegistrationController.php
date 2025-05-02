<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\VendorWelcomeMail;
use App\Mail\VerificationCodeMail;
use App\Services\TwilioSmsService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use WisdomDiala\Countrypkg\Models\Country;
use App\Services\VendorRegistrationService;
use App\Http\Requests\VendorRegisterRequest;
use App\Notifications\PhoneVerificationNotification;

class VendorRegistrationController extends Controller
{
    public function __construct(protected VendorRegistrationService $vendorRegistrationService, protected TwilioSmsService $twilioSmsService) {}

    public function create()
    {
        return view('auth.vendor-register');
    }

    /**
     * Send verification code to the phone number.
     */

    public function sendPhoneVerificationCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first('phone')
                ]);
            }

            return redirect()->route('vendor.register.step3')
                ->withErrors($validator)
                ->withInput();
        }

        // Generate a verification code (a 6-digit number)
        $verificationCode = (string) mt_rand(100000, 999999);

        // Store the code and its expiration time in the session
        Session::put('vendor_registration.phone_verification_code', $verificationCode);
        Session::put('vendor_registration.phone_code_expires_at', Carbon::now()->addMinutes(15)->timestamp);
        Session::put('vendor_registration.phone', $request->phone);

        // Send the SMS with the verification code using the service
        try {
            $this->twilioSmsService->sendVerificationCode($request->phone, $verificationCode);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Verification code sent! Please check your phone.'
                ]);
            }

            Session::flash('phone_verification_sent', true);
            return redirect()->route('vendor.register.step3')
                ->with('status', 'Verification code sent! Please check your phone.');
        } catch (\Exception $e) {
            Log::error('Failed to send verification SMS: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send verification code. Please try again.'
                ]);
            }

            return redirect()->route('vendor.register.step3')
                ->withErrors(['phone' => 'Failed to send verification code. Please try again.'])
                ->withInput();
        }
    }
    /**
     * Send verification code to the email address.
     */
    public function sendVerificationCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:users',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first('email')
                ]);
            }

            return redirect()->route('vendor.register.step2')
                ->withErrors($validator)
                ->withInput();
        }

        // Generate a verification code (a 6-digit number)
        $verificationCode = (string) mt_rand(100000, 999999);

        // Store the code and its expiration time in the session
        Session::put('vendor_registration.verification_code', $verificationCode);
        Session::put('vendor_registration.code_expires_at', Carbon::now()->addMinutes(15)->timestamp);
        Session::put('vendor_registration.email', $request->email);

        // Send the email with the verification code
        try {
            Mail::to($request->email)->send(new VerificationCodeMail($verificationCode));

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Verification code sent! Please check your email.'
                ]);
            }

            Session::flash('verification_sent', true);
            return redirect()->route('vendor.register.step2')
                ->with('status', 'Verification code sent! Please check your email.');
        } catch (\Exception $e) {
            Log::error('Failed to send verification email: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send verification code. Please try again.'
                ]);
            }

            return redirect()->route('vendor.register.step2')
                ->withErrors(['email' => 'Failed to send verification code. Please try again.'])
                ->withInput();
        }
    }

   

    /**
     * Show the step 1 form for vendor registration (country selection).
     */
    public function showStep1Form()
    {
        $countries = Country::query()->get();
        return view('auth.vendor-register-step1', compact('countries'));
    }


    /**
     * Process the step 1 form submission.
     */
    public function processStep1(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Store the country in session
        Session::put('vendor_registration.country', $request->country);

        return redirect()->route('vendor.register.step2');
    }

    /**
     * Show the step 2 form for vendor registration (email verification).
     */
    public function showStep2Form()
    {
        // Check if step 1 is completed
        if (!Session::has('vendor_registration.country')) {
            return redirect()->route('vendor.register.step1');
        }

        return view('auth.vendor-register-step2');
    }

    /**
     * Process the step 2 form submission (email verification).
     */
    public function processStep2(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:users',
            'verification_code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if code exists and hasn't expired
        $storedCode = Session::get('vendor_registration.verification_code');
        $expiresAt = Session::get('vendor_registration.code_expires_at');
        $storedEmail = Session::get('vendor_registration.email');

        if (!$storedCode || !$expiresAt) {
            return redirect()->back()
                ->withErrors(['verification_code' => 'Verification code not found. Please request a new code.'])
                ->withInput();
        }

        if (Carbon::now()->timestamp > $expiresAt) {
            return redirect()->back()
                ->withErrors(['verification_code' => 'Verification code has expired. Please request a new code.'])
                ->withInput();
        }

        if ($request->email !== $storedEmail) {
            return redirect()->back()
                ->withErrors(['email' => 'This email does not match the one used for the verification code.'])
                ->withInput();
        }

        if ($request->verification_code !== $storedCode) {
            return redirect()->back()
                ->withErrors(['verification_code' => 'Invalid verification code. Please try again.'])
                ->withInput();
        }

        // Email is verified, store it in the session
        Session::put('vendor_registration.email_verified', true);

        // update the email_verified_at field in the database

        // Clear the verification code from session after successful verification
        Session::forget(['vendor_registration.verification_code', 'vendor_registration.code_expires_at']);

        return redirect()->route('vendor.register.step3');
    }


    /**
     * Show the step 3 form for vendor registration (password and phone).
     */
    public function showStep3Form()
    {
        // Check if previous steps are completed
        if (
            !Session::has('vendor_registration.country') ||
            !Session::has('vendor_registration.email') ||
            !Session::get('vendor_registration.email_verified', false)
        ) {
            return redirect()->route('vendor.register.step1');
        }

        return view('auth.vendor-register-step3');
    }


    /**
     * Process the step 3 form submission.
     */
    public function processStep3(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|max:20',
            'verification_code' => 'required|string',
            'password' => 'required|string|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
        ], [
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if code exists and hasn't expired
        // $storedCode = Session::get('vendor_registration.phone_verification_code');
        // $expiresAt = Session::get('vendor_registration.phone_code_expires_at');
        $storedPhone = Session::get('vendor_registration.phone');

        // if (!$storedCode || !$expiresAt) {
        //     return redirect()->back()
        //         ->withErrors(['verification_code' => 'Verification code not found. Please request a new code.'])
        //         ->withInput();
        // }

        // if (Carbon::now()->timestamp > $expiresAt) {
        //     return redirect()->back()
        //         ->withErrors(['verification_code' => 'Verification code has expired. Please request a new code.'])
        //         ->withInput();
        // }

        if ($request->phone !== $storedPhone) {
            return redirect()->back()
                ->withErrors(['phone' => 'This phone number does not match the one used for the verification code.'])
                ->withInput();
        }

        // if ($request->verification_code !== $storedCode) {
        //     return redirect()->back()
        //         ->withErrors(['verification_code' => 'Invalid verification code. Please try again.'])
        //         ->withInput();
        // }

        // Phone is verified, store it in the session
        Session::put('vendor_registration.phone_verified', true);
        Session::put('vendor_registration.phone', $request->phone);
        Session::put('vendor_registration.password', $request->password);

        // Clear the verification code from session after successful verification
        Session::forget(['vendor_registration.phone_verification_code', 'vendor_registration.phone_code_expires_at']);

        return redirect()->route('vendor.register.step4');
    }

    /**
     * Show the step 4 form for vendor registration (shop details).
     */
    public function showStep4Form()
    {
        // Check if previous steps are completed
        if (
            !Session::has('vendor_registration.country') ||
            !Session::has('vendor_registration.email') ||
            !Session::has('vendor_registration.password') ||
            !Session::get('vendor_registration.email_verified', false)
        ) {
            return redirect()->route('vendor.register.step1');
        }

        return view('auth.vendor-register-step4');
    }

    /**
     * Complete the vendor registration process.
     */
    public function complete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_type' => 'required|string|in:business,individual',
            'shop_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'terms' => 'required|accepted',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if all previous steps are completed
        if (
            !Session::has('vendor_registration.country') ||
            !Session::has('vendor_registration.email') ||
            !Session::has('vendor_registration.password') ||
            !Session::get('vendor_registration.email_verified', false) ||
            !Session::has('vendor_registration.phone') ||
            !Session::get('vendor_registration.phone_verified', false)
        ) {
            return redirect()->route('vendor.register.step1')
                ->with('error', 'Please complete all previous steps first.');
        }

        // Get all registration data from session
        $data = [
            'shop_name' => $request->shop_name, // Using shop name as the user's name
            'email' => Session::get('vendor_registration.email'),
            'password' => Session::get('vendor_registration.password'),
            'country' => Session::get('vendor_registration.country'),
            'phone' => Session::get('vendor_registration.phone'),
            'address' => $request->location,
            'name' => $request->name,
            'account_type' => $request->account_type, // Store this if needed later
        ];

        try {
            // Register the vendor
            $user = $this->vendorRegistrationService->register($data);

            // Fire registered event
            // event(new Registered($user));

            // Login the user
            Auth::login($user);

            // Send welcome/confirmation email
            $this->sendWelcomeEmail($user);

            // Clear the registration session data
            Session::forget([
                'vendor_registration.country',
                'vendor_registration.email',
                'vendor_registration.email_verified',
                'vendor_registration.phone',
                'vendor_registration.phone_verified',
                'vendor_registration.password'
            ]);

            // Redirect to the pending approval page
            return redirect()->route('vendor.pending');
        } catch (\Exception $e) {
            Log::error('Vendor registration failed: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Registration failed. Please try again or contact support.')
                ->withInput();
        }
    }

    /**
     * Send welcome email to the newly registered vendor.
     */
    protected function sendWelcomeEmail($user)
    {
        try {
            // Implement this to send a welcome email
            Mail::to($user->email)->send(new VendorWelcomeMail($user));
        } catch (\Exception $e) {
            Log::error('Failed to send welcome email: ' . $e->getMessage());
            // Don't throw the exception, just log it
        }
    }

    /**
     * Show the pending approval page.
     */
    public function pending()
    {
        // Check if the user is logged in and is a vendor with inactive status
        if (!Auth::check() || Auth::user()->role !== 'vendor' || Auth::user()->status !== 'inactive') {
            return redirect()->route('vendor.login');
        }

        return view('auth.vendor-pending-approval');
    }
}
