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
        Schema::table('entry_products', function (Blueprint $table) {
            $table->integer('previous_stock')->after('added_stock');
            $table->integer('current_stock')->after('previous_stock');
        });
    
        Schema::table('exit_products', function (Blueprint $table) {
            $table->integer('previous_stock')->after('removed_stock');
            $table->integer('current_stock')->after('previous_stock');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entry_products', function (Blueprint $table) {
            $table->dropColumn(['previous_stock', 'current_stock']);
        });
    
        Schema::table('exit_products', function (Blueprint $table) {
            $table->dropColumn(['previous_stock', 'current_stock']);
        });
    }
};
