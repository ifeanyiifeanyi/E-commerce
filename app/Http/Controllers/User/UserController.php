<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Models\CustomerAddress;
use Illuminate\Validation\Rule;
use App\Models\CustomerActivityLog;
use App\Http\Controllers\Controller;
use App\Models\CustomerLoginHistory;
use App\Models\CustomerNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Models\Activity;
use WisdomDiala\Countrypkg\Models\Country;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\CustomerStoreAddressRequest;
use App\Http\Requests\CustomerUpdateAddressRequest;
use App\Http\Requests\CustomerChangePasswordRequest;

class UserController extends Controller
{
    public function __construct(protected UserService $userService) {}
    /**
     * Display customer dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Get recent activities
        $recentActivities = Activity::where('causer_id', $user->id)
            ->where('causer_type', User::class)
            ->latest()
            ->limit(5)
            ->get();

        // Get recent notifications
        $notifications = CustomerNotification::where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        // Get unread notifications count
        $unreadNotifications = CustomerNotification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->count();

        // Get recent login history
        $recentLogins = CustomerLoginHistory::where('user_id', $user->id)
            ->latest()
            ->limit(3)
            ->get();
        $customActivities = CustomerActivityLog::where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('user.dashboard', compact(
            'user',
            'recentActivities',
            'notifications',
            'unreadNotifications',
            'recentLogins',
            'customActivities'
        ));
    }


    /**
     * Show profile page
     */
    public function profile()
    {
        $user = Auth::user();
        $countries = Country::all();
        return view('user.profile.index', compact('user', 'countries'));
    }

    /**
     * Show addresses page
     */
    public function addresses()
    {
        $user = Auth::user();
        $countries = Country::all();
        $addresses = $user->addresses()->latest()->get();

        return view('user.profile.address', compact('addresses', 'user', 'countries'));
    }

    /**
     * Show security settings
     */
    public function security()
    {
        $user = Auth::user();
        $loginHistory = CustomerLoginHistory::where('user_id', $user->id)
            ->latest()
            ->limit(10)
            ->get();

        return view('user.security.index', compact('user', 'loginHistory'));
    }

    /**
     * Show activity log
     */
    public function activityLog()
    {
        $user = Auth::user();

        // Get Spatie activities
        $activities = Activity::where('causer_id', $user->id)
            ->where('causer_type', User::class)
            ->latest()
            ->paginate(10, ['*'], 'spatie_page');

        // Get custom activities`
        $customActivities = CustomerActivityLog::where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('user.profile.activity', compact('activities', 'customActivities'));
    }



    /**
     * Show notifications
     */
    public function notifications()
    {
        $user = Auth::user();
        $notifications = CustomerNotification::where('user_id', $user->id)
            ->latest()
            ->paginate(20);

        return view('user.notifications.index', compact('notifications'));
    }


    /**
     * Update profile information
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = Auth::user();



        
        $data = $request->only([
            'name',
            'username',
            'email',
            'phone',
            'address',
            'city',
            'state',
            'postal_code',
            'country',
            'marketing_preferences'
        ]);
        $updatedUser = $this->userService->updateProfile(
            $user,
            $data,
            $request->file('photo')
        );

        return to_route('user.profile')->with('success', 'Profile updated successfully!');
    }

    /**
     * Change password
     */
    public function changePassword(CustomerChangePasswordRequest $request)
    {
        $user = Auth::user();

        $this->userService->changePassword($user, $request->password);

        return to_route('user.profile')->with('success', 'Password changed successfully!');
    }

    /**
     * Store new address
     */
    public function storeAddress(CustomerStoreAddressRequest $request)
    {
        $user = Auth::user();

        $address = $this->userService->createAddress($user, $request->validated());

        return to_route('user.addresses')->with('success', 'Address added successfully!');
    }

    /**
     * Update address
     */
    public function updateAddress(CustomerUpdateAddressRequest $request, CustomerAddress $address)
    {
        $updatedAddress = $this->userService->updateAddress($address, $request->validated());

        return to_route('user.addresses')
            ->with('success', 'Address updated successfully!');
    }

    /**
     * Delete address
     */
    public function deleteAddress(CustomerAddress $address)
    {
        // Ensure the address belongs to the authenticated user
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $this->userService->deleteAddress($address);

        return to_route('user.addresses')->with('success', 'Address deleted successfully!');
    }

    /**
     * Mark notification as read
     */
    public function markNotificationAsRead(CustomerNotification $notification)
    {
        // Ensure the notification belongs to the authenticated user
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsAsRead()
    {
        CustomerNotification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        // Log bulk notification read activity
        CustomerActivityLog::log(
            userId: Auth::id(),
            activityType: 'notifications_bulk_read',
            description: 'All notifications marked as read',
            properties: [
                'event' => 'notifications_bulk_read',
                'subject_type' => CustomerNotification::class,
            ]
        );

        // Spatie activity log
        activity()
            ->causedBy(Auth::user())
            ->withProperties([
                'event' => 'notifications_bulk_read',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log('All notifications marked as read');

        return redirect()->route('user.notifications')
            ->with('success', 'All notifications marked as read!');
    }



    /**
     * Update password
     */
    public function logout()
    {
        $user = Auth::user();

        // Log logout activity before logging out
        CustomerActivityLog::log(
            userId: $user->id,
            activityType: 'user_logout',
            description: 'User logged out',
            properties: [
                'event' => 'user_logout',
                'subject_type' => User::class,
                'subject_id' => $user->id,
            ]
        );

        // Spatie activity log
        activity()
            ->causedBy($user)
            ->withProperties([
                'event' => 'user_logout',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log('User logged out');

        Auth::logout();

        // Clear the session
        session()->flush();

        // Regenerate the session token to prevent session fixation attacks
        session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'You have been logged out successfully.');
    }
}
