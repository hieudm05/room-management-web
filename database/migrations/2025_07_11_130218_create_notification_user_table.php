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
        Schema::create('notification_user', function (Blueprint $table) {
            $table->id(); // ID bản ghi
            $table->unsignedBigInteger('notification_id');
            $table->unsignedBigInteger('user_id');

            $table->boolean('is_read')->default(false); // Đã đọc chưa
            $table->timestamp('read_at')->nullable(); // Thời điểm đọc
            $table->timestamp('received_at')->useCurrent(); // Thời điểm nhận
            $table->timestamps(); // Thời gian tạo và cập nhật
            // Liên kết khóa ngoại
            $table->foreign('notification_id')->references('id')->on('notifications')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_user');
    }
};
