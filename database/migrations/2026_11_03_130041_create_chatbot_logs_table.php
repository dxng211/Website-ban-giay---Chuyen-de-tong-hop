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
        Schema::create('chatbot_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // NULL nếu Guest
            $table->string('session_token', 100);
            $table->string('need_type', 50)->nullable();       // NULL khi hỏi size ở trang chi tiết (không có form nhu cầu)
            $table->decimal('foot_length_cm', 4, 1);
            $table->decimal('price_min', 12, 0)->nullable();
            $table->decimal('price_max', 12, 0)->nullable();
            $table->json('suggested_product_ids')->nullable(); // NULL khi không có ứng viên
            $table->text('ai_reply')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_logs');
    }
};
