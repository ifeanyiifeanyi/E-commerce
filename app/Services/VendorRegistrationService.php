<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class VendorRegistrationService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Register a new vendor user.
     *
     * @param array $data
     * @return User
     */
    public function register(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'username' => 'vendor'.rand(0000, 9999).date('y'),
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'vendor',
            'status' => 'inactive', // Vendors start as inactive until approved
        ]);
    }
}
