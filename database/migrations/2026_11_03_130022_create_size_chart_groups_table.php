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
        Schema::create('size_chart_groups', function (Blueprint $table) {
            $table->string('chart_key', 30)->primary();       // VD: shoe_vn, sock_vn
            $table->string('chart_name', 100);
            $table->string('size_column_label', 50);
            $table->string('reference_column_label', 50);
            $table->string('unit', 20)->nullable();           // VD: cm, inch
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('size_chart_groups');
    }
};
