<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['stock', 'exp_date']);
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('stock');
            $table->date('exp_date');
        });
    }
};
