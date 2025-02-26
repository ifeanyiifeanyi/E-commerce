<?php

namespace App\Http\Controllers\Vendor;

use Illuminate\Http\Request;
use App\Services\SessionService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\VendorProfileService;
use App\Http\Requests\VendorProfilePhotoRequest;
use App\Http\Requests\VendorProfileUpdateRequest;
use App\Http\Requests\VendorPasswordUpdateRequest;

class VendorProfileController extends Controller
{
    public function __construct(
        private VendorProfileService $profileService,
        private SessionService $sessionService
    ) {}


    public function index()
    {
        $user = request()->user();
        $sessions = $this->sessionService->getActiveSessions();
        $lastLogin = $this->sessionService->getLastLogin($user->id);

        return view('vendor.profile.index', compact('user', 'sessions', 'lastLogin'));
    }

    public function update(VendorProfileUpdateRequest $request)
    {
        $this->profileService->updateProfile($request->validated());

        return redirect()->route('vendor.profile')
            ->with('success', 'Profile information updated successfully.');
    }

    public function updatePassword(VendorPasswordUpdateRequest $request)
    {
        $this->profileService->updatePassword($request->validated());

        return redirect()->route('vendor.profile')
            ->with('password_success', 'Password updated successfully.');
    }

    public function updatePhoto(VendorProfilePhotoRequest $request)
    {
        $this->profileService->updateProfilePhoto($request);

        return redirect()->route('vendor.profile')
            ->with('success', 'Profile photo updated successfully.');
    }

    public function deletePhoto()
    {
        $this->profileService->deleteProfilePhoto();

        return redirect()->route('vendor.profile')
            ->with('success', 'Profile photo deleted successfully.');
    }

    public function destroySession($sessionId)
    {
        $this->sessionService->destroySession($sessionId);

        return redirect()->route('vendor.profile')
            ->with('success', 'Device logged out successfully.');
    }
}
