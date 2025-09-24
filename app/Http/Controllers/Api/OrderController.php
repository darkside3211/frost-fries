<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    /**
     * Store a newly created order in storage.
     */
    public function store(Request $request)
    {
        // 1. Validate the incoming data (array of products with quantities)
        $validatedData = $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            // Pro Tip: Wrap all database operations in a transaction!
            $order = DB::transaction(function () use ($validatedData, $request) {
                $totalAmount = 0;

                // 2. Calculate the final total on the backend
                foreach ($validatedData['items'] as $itemData) {
                    $product = Product::findOrFail($itemData['product_id']);

                    // 3. Check for sufficient stock
                    if ($product->stock_quantity < $itemData['quantity']) {
                        // Throw a validation exception if stock is insufficient
                        throw ValidationException::withMessages([
                           'items' => "Not enough stock for product: {$product->name}",
                        ]);
                    }

                    $totalAmount += $product->price * $itemData['quantity'];
                }

                // 4. Create a new record in the 'orders' table
                $order = Order::create([
                    'user_id' => $request->user()->id, // Get the authenticated user's ID
                    'total_amount' => $totalAmount,
                ]);

                // 5. Create records for each item in 'order_items'
                foreach ($validatedData['items'] as $itemData) {
                    $product = Product::findOrFail($itemData['product_id']);
                    $order->items()->create([
                        'product_id' => $product->id,
                        'quantity' => $itemData['quantity'],
                        'price' => $product->price, // Price at the time of sale
                    ]);

                    // 6. Decrement the stock for each product
                    $product->decrement('stock_quantity', $itemData['quantity']);
                }

                return $order;
            });

             // 7. Return a success response with the created order
            return response()->json($order->load('items'), 201); // 201 Created

        } catch (ValidationException $e) {
            // Return a 422 Unprocessable Entity error if validation fails
            return response()->json(['message' => $e->getMessage(), 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            // Return a generic server error for any other exceptions
            return response()->json(['message' => 'An unexpected error occurred.'], 500);
        }
    }
}