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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();

            // Menyimpan siapa yang membuat cart
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->date('exp_date')->nullable();
            $table->integer('quantity');
            $table->decimal('gross_amount', 15, 2);

            // Menyimpan metode pengiriman dan pembayaran yang dipilih user
            $table->enum('shipping_method', ['Reguler', 'Express', 'Ambil di tempat'])->nullable()->default(null);
            $table->enum('payment_method', ['Cash', 'Bank Transfer', 'OVO', 'Dana', 'COD'])->default('Cash');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
