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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code', 30)->unique();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('recipient_name');                  // snapshot thông tin giao hàng
            $table->string('recipient_phone');
            $table->string('shipping_address');
            $table->enum('payment_method', ['cod', 'bank_transfer']);
            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid');
            $table->enum('order_status', ['pending', 'shipping', 'completed', 'cancelled'])->default('pending');
            $table->decimal('total_amount', 12, 0);
            $table->string('note')->nullable();
            $table->timestamps();

            $table->index('order_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
