<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;
use App\Services\TwilioSmsService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class VendorRegistrationService
{
    /**
     * Create a new service instance.
     */
    public function __construct(protected TwilioSmsService $twilioService)
    {
    }
    /**
     * Register a new vendor user.
     *
     * @param array $data
     * @return User
     */
    public function register(array $data): User
    {
        // Generate a unique username based on shop name
        $shopName = $data['shop_name'] ?? 'vendor';
        $username = Str::slug($shopName) . '-' . rand(1000, 9999);

        // Create the user with vendor role
        $user = User::create([
            'name' => $data['shop_name'] ?? $data['name'], // Use shop_name as user's name if available
            'username' => $username,
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'] ?? null,
            'country' => $data['country'] ?? null,
            'account_type' => $data['account_type'] ?? null,
            'role' => 'vendor',
            'status' => 'inactive', // Vendors start as inactive until approved
            'address' => $data['address'] ?? null,
            'email_verified_at' => now(),

        ]);

        if (!empty($user->phone)) {
            try {
                $this->twilioService->sendWelcome($user->phone, $user->name);
            } catch (\Exception $e) {
                // Log but don't throw the exception for welcome messages
                Log::error('Failed to send welcome SMS: ' . $e->getMessage());
            }
        }

        return $user;
    }
}
