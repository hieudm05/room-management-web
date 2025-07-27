<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveRoomUtilityIdFromRoomUtilityPhotosTable extends Migration
{
    public function up()
    {
        Schema::table('room_utility_photos', function (Blueprint $table) {
            // Xoá ràng buộc khóa ngoại trước
            $table->dropForeign(['room_utility_id']);

            // Sau đó mới xoá cột
            $table->dropColumn('room_utility_id');
        });
    }

    public function down()
    {
        Schema::table('room_utility_photos', function (Blueprint $table) {
            // Tạo lại cột
            $table->unsignedBigInteger('room_utility_id')->nullable();

            // Tạo lại khóa ngoại nếu cần
            $table->foreign('room_utility_id')->references('id')->on('room_utilities')->onDelete('cascade');
        });
    }
}
