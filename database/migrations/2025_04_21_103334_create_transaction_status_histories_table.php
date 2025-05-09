<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaction_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade');
            $table->enum('status', ['Pesanan selesai', 'Pembayaran lunas', 'Belum bayar', 'Pesanan sampai', 'Dalam perjalanan', 'Pesanan selesai dikemas', 'Dalam pengemasan', 'Pembayaran gagal', 'Dibatalkan', 'Proses pembatalan', 'Pembatalan berhasil', 'Pengiriman gagal']);
            $table->timestamp('changed_at')->default(DB::raw('CURRENT_TIMESTAMP')); // waktu perubahan status
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_status_histories');
    }
};
