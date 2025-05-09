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
        Schema::create('product_status_history_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_status_history_id')->constrained()->onDelete('cascade');
            $table->date('product_exp_date');
            $table->integer('product_stock');
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_status_history_details');
    }
};
