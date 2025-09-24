<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create 50 random products using the ProductFactory.
        Product::factory(50)->create();

        // Create or update the Admin user.
        User::updateOrCreate(
            ['email' => 'admin@frostfries.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Create or update the Cashier user.
        User::updateOrCreate(
            ['email' => 'cashier@frostfries.com'],
            [
                'name' => 'Cashier User',
                'password' => Hash::make('password'),
                'role' => 'cashier',
                'email_verified_at' => now(),
            ]
        );
    }
}
