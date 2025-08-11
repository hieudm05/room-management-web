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
        Schema::table('room_users', function (Blueprint $table) {
            // Xoá foreign key cũ (nếu có tên mặc định)
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id']);

            // Thêm unique và foreign key đúng
            $table->unsignedBigInteger('user_id')->nullable()->unique()->change();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
