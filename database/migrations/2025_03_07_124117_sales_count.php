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
        //
        Schema::create('sales_count', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->date('transaction_date');
            $table->integer('raw_sales');
            $table->double('weighted_sales');
            $table->integer('days_between_first_last_transaction');
            $table->double('time_weight');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
