<?php

namespace App\Services;

use App\Models\User;
use App\Models\CustomerAddress;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserService
{
    /**
     * Update user profile with photo handling
     */
    public function updateProfile(User $user, array $data, ?UploadedFile $photo = null): User
    {
        // Handle photo upload
        if ($photo) {
            $data['photo'] = $this->handlePhotoUpload($user, $photo);
        }

        // Handle marketing preferences
        $data['marketing_preferences'] = $data['marketing_preferences'] ?? [];

        $user->update($data);

        return $user->fresh();
    }

    /**
     * Change user password
     */
    public function changePassword(User $user, string $newPassword): User
    {
        $user->update([
            'password' => Hash::make($newPassword)
        ]);

        return $user->fresh();
    }

    /**
     * Handle photo upload and delete old photo
     */
    private function handlePhotoUpload(User $user, UploadedFile $photo): string
    {
        // Delete old photo if exists
        if ($user->photo && Storage::disk('public')->exists(str_replace('storage/', '', $user->photo))) {
            Storage::disk('public')->delete(str_replace('storage/', '', $user->photo));
        }

        $photoPath = $photo->store('uploads/customers', 'public');
        return 'storage/' . $photoPath;
    }


    /**
     * Create new address for user
     */
    public function createAddress(User $user, array $data): CustomerAddress
    {
        // If this is set as default, remove default from other addresses
        if ($data['is_default'] ?? false) {
            $user->addresses()->update(['is_default' => false]);
        }

        return $user->addresses()->create($data);
    }
    /**
     * Update existing address
     */
    public function updateAddress(CustomerAddress $address, array $data): CustomerAddress
    {
        // If this is set as default, remove default from other addresses
        if ($data['is_default'] ?? false) {
            $address->user->addresses()
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);
        }

        $address->update($data);

        return $address->fresh();
    }

    /**
     * Delete address
     */
    public function deleteAddress(CustomerAddress $address): bool
    {
        return $address->delete();
    }
}
