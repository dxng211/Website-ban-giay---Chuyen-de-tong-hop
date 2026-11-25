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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->enum('group_type', ['shoe', 'accessory']);
            $table->string('slug', 120)->unique();
            $table->string('size_chart_key', 30)->nullable(); // NULL = không cần bảng hướng dẫn size

            $table->foreign('size_chart_key')
                ->references('chart_key')->on('size_chart_groups')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
