<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('staff_posts', function (Blueprint $table) {
            $table->unsignedBigInteger('room_id')->nullable()->after('property_id');

            // Nếu bạn muốn thiết lập ràng buộc khóa ngoại
            $table->foreign('room_id')->references('room_id')->on('rooms')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('staff_posts', function (Blueprint $table) {
            $table->dropForeign(['room_id']);
            $table->dropColumn('room_id');
        });
    }
};
