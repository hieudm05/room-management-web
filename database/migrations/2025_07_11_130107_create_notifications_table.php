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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id(); // ID thông báo
            $table->string('title', 255); // Tiêu đề
            $table->text('message'); // Nội dung
            $table->enum('type', ['system', 'user', 'alert', 'reminder'])->default('user');
            $table->string('link', 255)->nullable(); // Link chuyển đến
            $table->boolean('is_global')->default(false); // Gửi cho toàn bộ người dùng?
            $table->timestamp('expired_at')->nullable(); // Ngày hết hạn nếu có
            $table->timestamps(); // created_at, updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
