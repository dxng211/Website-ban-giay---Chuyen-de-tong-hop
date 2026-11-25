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
        Schema::create('size_charts', function (Blueprint $table) {
            $table->id();
            $table->string('chart_key', 30);
            $table->string('size_label', 20);                 // không unique: 1 size_label có thể lặp nhiều dòng
            $table->string('reference_value', 30)->nullable(); // NULL = hệ thống tự sinh từ min/max + unit
            $table->decimal('reference_min', 5, 2);
            $table->decimal('reference_max', 5, 2);
            $table->unsignedInteger('sort_order')->default(0);

            $table->foreign('chart_key')
                ->references('chart_key')->on('size_chart_groups')
                ->cascadeOnDelete();
            $table->index(['chart_key', 'reference_min', 'reference_max']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('size_charts');
    }
};
