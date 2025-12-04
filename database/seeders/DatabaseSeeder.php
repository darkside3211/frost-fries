<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Create Users (So you can log in!)
        // Admin / Owner Account
        User::create([
            'name' => 'Owner Admin',
            'email' => 'admin@frostfries.com',
            'password' => bcrypt('password'),
            'role' => 'admin', 
        ]);

        // Cashier Account
        User::create([
            'name' => 'John Cashier',
            'email' => 'cashier@frostfries.com',
            'password' => bcrypt('password'),
            'role' => 'cashier',
        ]);

        // 2. Create Products
        // Create 20 random products
        $products = Product::factory(20)->create();

        // Create 5 specific "Low Stock" products (Stock < 10) for testing alerts
        $lowStockProducts = Product::factory(5)->create([
            'stock_quantity' => 3, 
            'name' => function() { return 'Low Stock ' . fake()->word; }
        ]);

        // Merge all products so we can sell them below
        $allProducts = $products->merge($lowStockProducts);

        // 3. Create Orders (Sales History)
        
        // Create 10 orders for "Today" (To test Daily Report)
        for ($i = 0; $i < 10; $i++) {
            $this->createOrder($allProducts, Carbon::today());
        }

        // Create 10 orders for "Last Week" (To test Weekly/Monthly Report)
        for ($i = 0; $i < 10; $i++) {
            $this->createOrder($allProducts, Carbon::now()->subDays(5));
        }
    }

    /**
     * Helper function to create a fake order with items
     */
    private function createOrder($products, $date)
    {
        // Create the Order shell
        $order = Order::create([
            'user_id' => 2, // Assign to the Cashier (ID 2)
            'total_amount' => 0, // Placeholder, calculated below
            'created_at' => $date,
            'updated_at' => $date,
        ]);

        $total = 0;
        
        // Add 1-3 random items to this order
        $numberOfItems = rand(1, 3);
        
        for ($j = 0; $j < $numberOfItems; $j++) {
            $product = $products->random();
            $qty = rand(1, 3);
            $price = $product->price;

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $qty,
                'price' => $price,
            ]);

            $total += ($price * $qty);
        }

        // Update the total price
        $order->update(['total_amount' => $total]);
    }
}