<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeToRoomUtilityPhotosTable extends Migration
{
    public function up()
    {
        Schema::table('room_utility_photos', function (Blueprint $table) {
            $table->enum('type', ['electric', 'water'])->after('room_utility_id');
        });
    }

    public function down()
    {
        Schema::table('room_utility_photos', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}