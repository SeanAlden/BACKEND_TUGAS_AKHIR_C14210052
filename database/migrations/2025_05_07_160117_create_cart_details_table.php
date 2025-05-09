<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cart_details', function (Blueprint $table) {
            $table->id();
        
            // Relasi ke tabel carts
            $table->foreignId('cart_id')->constrained('carts')->onDelete('cascade');
        
            // Relasi ke product_stocks berdasarkan exp_date & product_id
            $table->foreignId('product_stock_id')->constrained('product_stocks')->onDelete('cascade');
        
            $table->integer('quantity');
            $table->decimal('gross_amount', 15, 2);
        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_details');
    }
};
