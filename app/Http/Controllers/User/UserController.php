<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\CustomerAddress;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\CustomerLoginHistory;
use App\Models\CustomerNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Models\Activity;
use WisdomDiala\Countrypkg\Models\Country;

class UserController extends Controller
{
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

        return view('user.dashboard', compact(
            'user',
            'recentActivities',
            'notifications',
            'unreadNotifications',
            'recentLogins'
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
        $activities = Activity::where('causer_id', $user->id)
            ->where('causer_type', User::class)
            ->latest()
            ->paginate(20);

        return view('user.profile.activity', compact('activities'));
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
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'marketing_preferences' => 'nullable|array',
            'marketing_preferences.*' => 'string|in:email,sms,push,newsletter',
        ]);

        $data = $request->only([
            'name',
            'username',
            'email',
            'phone',
            'address',
            'city',
            'state',
            'postal_code',
            'country'
        ]);

        // Handle marketing preferences
        $data['marketing_preferences'] = $request->input('marketing_preferences', []);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($user->photo && Storage::disk('public')->exists(str_replace('storage/', '', $user->photo))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $user->photo));
            }

            $photoPath = $request->file('photo')->store('uploads/customers', 'public');
            $data['photo'] = 'storage/' . $photoPath;
        }

        $user->update($data);

        // Log activity
        activity()
            ->causedBy($user)
            ->log('Profile updated');

        return redirect()->route('user.profile')
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->current_password && Hash::check($value, Auth::user()->password)) {
                        $fail('The new password must be different from the current password.');
                    }
                },
            ],
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // Log activity
        activity()
            ->causedBy($user)
            ->log('Password changed');

        return redirect()->route('user.profile')
            ->with('success', 'Password changed successfully!');
    }

    /**
     * Store new address
     */
    public function storeAddress(Request $request)
    {
        $request->validate([
            'address_type' => 'required|in:billing,shipping,both',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'is_default' => 'boolean',
        ]);

        $user = Auth::user();

        // If this is set as default, remove default from other addresses
        if ($request->is_default) {
            $user->addresses()->update(['is_default' => false]);
        }

        $address = $user->addresses()->create($request->all());

        // Log activity
        activity()
            ->causedBy($user)
            ->performedOn($address)
            ->log('New address added');

        return redirect()->route('user.addresses')
            ->with('success', 'Address added successfully!');
    }

    /**
     * Update address
     */
    public function updateAddress(Request $request, CustomerAddress $address)
    {
        // Ensure the address belongs to the authenticated user
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'address_type' => 'required|in:billing,shipping,both',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'is_default' => 'boolean',
        ]);

        // If this is set as default, remove default from other addresses
        if ($request->is_default) {
            Auth::user()->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        $address->update($request->all());

        // Log activity
        activity()
            ->causedBy(Auth::user())
            ->performedOn($address)
            ->log('Address updated');

        return redirect()->route('user.addresses')
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

        // Log activity before deletion
        activity()
            ->causedBy(Auth::user())
            ->performedOn($address)
            ->log('Address deleted');

        $address->delete();

        return redirect()->route('user.addresses')
            ->with('success', 'Address deleted successfully!');
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
    public function markAllNotificationsAsRead()
    {
        CustomerNotification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return redirect()->route('user.notifications')
            ->with('success', 'All notifications marked as read!');
    }



    /**
     * Update password
     */
    public function logout()
    {
        Auth::logout();
        // Clear the session
        session()->flush();
        // Regenerate the session token to prevent session fixation attacks
        session()->regenerateToken();



        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }
}
