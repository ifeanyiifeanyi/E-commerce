<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\AdminProfileUpdateRequest;

class AdminProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = request()->user();
        $sessions = $this->getActiveSessions();

        return view('admin.profile.index', compact('user', 'sessions'));
    }


    /**
     * Update the specified resource in storage.
     */

    public function update(AdminProfileUpdateRequest $request)
    {
        request()->user()->update($request->validated());

        return redirect()->back()->with('success', 'Profile updated successfully');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = request()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The provided password does not match your current password.']);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->back()->with('success', 'Password updated successfully');
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|max:2048' // 2MB max
        ]);

        $user = request()->user();

        // Create directory if it doesn't exist
        $directory = public_path('uploads/profile-photos');
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        // Delete old photo if exists
        if ($user->photo && file_exists(public_path($user->photo))) {
            unlink(public_path($user->photo));
        }

        // Process and save new photo
        $image = $request->file('photo');
        $filename = 'uploads/profile-photos/' . uniqid() . '.' . $image->getClientOriginalExtension();

        // Create new ImageManager instance with desired driver
        $manager = new ImageManager(new Driver());

        // Resize and save image
        $manager->read($image)
            ->cover(300, 300)
            ->save(public_path($filename));

        $user->update(['photo' => $filename]);

        return response()->json(['success' => true]);
    }
    public function deletePhoto()
    {
        $user = request()->user();

        if ($user->photo && file_exists(public_path($user->photo))) {
            unlink(public_path($user->photo));
        }

        $user->update(['photo' => null]);

        return response()->json(['success' => true]);
    }





    public function logoutSession(Request $request)
    {
        $request->validate(['session_id' => 'required|string']);

        if ($request->session_id !== session()->getId()) {
            DB::table('sessions')
                ->where('id', $request->session_id)
                ->where('user_id', request()->user()->id)
                ->delete();
        }

        return redirect()->back()->with('success', 'Session logged out successfully');
    }

    // private function getActiveSessions()
    // {
    //     $sessions = DB::table('sessions')
    //         ->where('user_id', request()->user()->id)
    //         ->get();

    //     $currentSessionId = session()->getId();
    //     $agent = new Agent();

    //     return $sessions->map(function ($session) use ($currentSessionId, $agent) {
    //         $agent->setUserAgent($session->user_agent);

    //         return (object)[
    //             'id' => $session->id,
    //             'ip_address' => $session->ip_address,
    //             'device' => $agent->device() . ' - ' . $agent->browser(),
    //             'device_type' => $this->getDeviceIcon($agent),
    //             'last_activity' => now()->createFromTimestamp($session->last_activity)->diffForHumans(),
    //             'is_current' => $session->id === $currentSessionId
    //         ];
    //     });
    // }

    private function getActiveSessions()
    {
        $sessions = DB::table('sessions')
            ->where('user_id', request()->user()->id)
            ->get();

        $currentSessionId = session()->getId();

        return $sessions->map(function ($session) use ($currentSessionId) {
            $userAgent = $session->user_agent;

            // Simple device detection
            $device = 'Desktop';
            $deviceType = 'desktop';

            if (strpos(strtolower($userAgent), 'mobile') !== false) {
                $device = 'Mobile';
                $deviceType = 'mobile';
            } elseif (strpos(strtolower($userAgent), 'tablet') !== false) {
                $device = 'Tablet';
                $deviceType = 'tablet';
            }

            // Simple browser detection
            $browser = 'Unknown';
            $browsers = [
                'Chrome' => 'Chrome',
                'Firefox' => 'Firefox',
                'Safari' => 'Safari',
                'Edge' => 'Edge',
                'MSIE' => 'Internet Explorer',
                'Opera' => 'Opera'
            ];

            foreach ($browsers as $key => $value) {
                if (strpos($userAgent, $key) !== false) {
                    $browser = $value;
                    break;
                }
            }

            return (object)[
                'id' => $session->id,
                'ip_address' => $session->ip_address,
                'device' => "$device - $browser",
                'device_type' => $deviceType,
                'last_activity' => now()->createFromTimestamp($session->last_activity)->diffForHumans(),
                'is_current' => $session->id === $currentSessionId
            ];
        });
    }


    private function getDeviceIcon($deviceType)
    {
        switch ($deviceType) {
            case 'mobile':
                return 'mobile';
            case 'tablet':
                return 'tablet';
            case 'desktop':
                return 'desktop';
            default:
                return 'question';
        }
    }
}
