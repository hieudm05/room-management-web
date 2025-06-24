<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_infos', function (Blueprint $table) {
            // thêm cột id_room
            $table->unsignedBigInteger('room_id')->nullable()->after('user_id');

            // Tạo khóa ngoại tham chiếu đến room_id của bảng rooms
            $table->foreign('room_id')
                  ->references('room_id')
                  ->on('rooms')
                  ->onDelete('set null'); // hoặc cascade tùy bạn
        });
    }

    public function down(): void
    {
        Schema::table('user_infos', function (Blueprint $table) {
            // xóa ràng buộc khóa ngoại trước
            $table->dropForeign(['room_id']);
            $table->dropColumn('room_id');
        });
    }
};
