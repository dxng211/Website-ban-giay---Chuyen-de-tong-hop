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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->string('sku', 50)->unique();
            $table->string('name', 200);
            $table->text('description')->nullable();
            $table->enum('gender', ['male', 'female', 'unisex'])->nullable(); // NULL với phụ kiện
            $table->decimal('base_price', 12, 0);
            $table->decimal('sale_price', 12, 0)->nullable();                 // NULL = không giảm giá
            $table->unsignedInteger('sold_count')->default(0);                // cache, Observer cập nhật
            $table->decimal('rating_avg', 2, 1)->default(0);                  // cache, chỉ tính review approved
            $table->unsignedInteger('review_count')->default(0);              // cache
            $table->boolean('is_active')->default(true);
            $table->softDeletes();                                            // BR2-UC09
            $table->timestamps();

            $table->index('sold_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
