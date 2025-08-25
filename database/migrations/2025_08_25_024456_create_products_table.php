<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::create('products', function (Blueprint $table) {
        $table->id(); // Unique ID for each product
        $table->string('name');
        $table->text('description')->nullable(); // nullable means it's optional
        $table->decimal('price', 8, 2); // Up to 8 digits, with 2 decimal places
        $table->integer('stock_quantity')->default(0);
        $table->string('sku')->unique()->nullable(); // Stock Keeping Unit
        $table->timestamps(); // created_at and updated_at columns
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
