<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Image;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\VendorProfilePhotoRequest;

class VendorProfileService
{
    public function updateProfile(array $data): void
    {
        $user = request()->user();
        $user->update($data);
    }

    public function updatePassword(array $data): void
    {
        $user = request()->user();
        $user->update([
            'password' => Hash::make($data['password']),
        ]);
    }

    public function updateProfilePhoto(VendorProfilePhotoRequest $request): void
    {
        $user = request()->user();

        // Delete the old photo if it exists
        if ($user->photo && Storage::disk('public')->exists(str_replace('storage/', '', $user->photo))) {
            Storage::disk('public')->delete(str_replace('storage/', '', $user->photo));
        }

        $image = $request->file('photo');

        // For Intervention Image 3.x
        $manager = new ImageManager(new Driver());
        $croppedImage = $manager->read($image->getRealPath());

        if ($request->filled(['x', 'y', 'width', 'height'])) {
            $croppedImage->crop(
                (int) $request->input('width'),
                (int) $request->input('height'),
                (int) $request->input('x'),
                (int) $request->input('y')
            );
        }

        // Resize image to standard size
        $croppedImage->cover(300, 300);

        // Generate unique filename
        $filename = 'profile-photos/' . uniqid() . '.jpg';

        // Store the image
        Storage::disk('public')->put($filename, $croppedImage->toJpeg(80)->toString());

        // Update user profile
        $user->update([
            'photo' => 'storage/' . $filename,
        ]);
    }
    public function deleteProfilePhoto(): void
    {
        $user = request()->user();

        if ($user->photo && Storage::disk('public')->exists(str_replace('storage/', '', $user->photo))) {
            Storage::disk('public')->delete(str_replace('storage/', '', $user->photo));
        }

        $user->update([
            'photo' => null,
        ]);
    }
}
