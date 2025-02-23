<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          $users = [
                [
                    'name' => 'Admin User',
                    'username' => 'admin_user',
                    'email' => 'admin@admin.com',
                    'phone' => '1234567890',
                    'address' => '123 Main St',
                    'role' => 'admin',
                    'status' => 'active',
                    'email_verified_at' => now(),
                    'password' => bcrypt('password'),
                ],
                [
                    'name' => 'Vendor User',
                    'username' => 'vendor_user',
                    'email' => 'vendor@vendor.com',
                    'phone' => '0987654321',
                    'address' => '456 Elm St',
                    'role' =>'vendor',
                    'status' => 'active',
                    'email_verified_at' => now(),
                    'password' => bcrypt('password'),

                ],
                [
                    'name' => 'User John',
                    'username' => 'user_john',
                    'email' => 'user_john@example.com',
                    'phone' => '1234567890',
                    'address' => '789 Oak St',
                    'role' => 'user',
                    'status' => 'active',
                    'email_verified_at' => now(),
                    'password' => bcrypt('password'),
                ]
                // Add more user records as needed
            ];

            DB::table('users')->insert($users);

    }
}
