<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(50)->create();

        // Uncomment the below line to run the UserSeeder with 100 users
        $this->call(
            UsersTableSeeder::class,
            MeasurementUnitSeeder::class
        );
    }
}

