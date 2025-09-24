<?php

namespace Database\Seeders;

use App\Models\Product; // You need this to create products
use App\Models\User;    // You need this to create users
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Step 1: Create 50 random products using the ProductFactory.
        // This line requires you to have created 'ProductFactory.php' first.
        Product::factory(50)->create();

        // Step 2: Create a specific Admin user for testing.
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@frostfries.com', // A more specific email
            'role' => 'admin' // IMPORTANT: Assign the 'admin' role
        ]);

        // Step 3 (Optional): Create a specific Cashier user for testing.
        User::factory()->create([
            'name' => 'Cashier User',
            'email' => 'cashier@frostfries.com',
            'role' => 'cashier' // Assign the 'cashier' role
        ]);
    }
}