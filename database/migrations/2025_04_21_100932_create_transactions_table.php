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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code')->unique();
            $table->enum('status', ['Pesanan selesai', 'Pembayaran lunas', 'Belum bayar', 'Pesanan sampai', 'Dalam perjalanan', 'Pesanan selesai dikemas', 'Dalam pengemasan', 'Pembayaran gagal', 'Dibatalkan', 'Proses pembatalan', 'Pembatalan berhasil', 'Pengiriman gagal'])->default('Dalam pengemasan');
            $table->foreignId('user_id')->constrained('users');
            $table->decimal('gross_amount', 15, 2);
            $table->decimal('shipping_cost', 15, 2)->default(0);
            $table->decimal('total_payment', 15, 2);
            $table->enum('shipping_method', ['Reguler', 'Express', 'Ambil di tempat'])->nullable()->default(null);
            $table->enum('payment_method', ['Cash', 'Bank Transfer', 'OVO', 'Dana', 'COD'])->default('Cash');

            // Waktu transaksi dan estimasi pengiriman
            $table->timestamp('shipping_time')->nullable(); // ⬅️ Tambahan di sini
            $table->timestamp('transaction_date')->useCurrent();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
