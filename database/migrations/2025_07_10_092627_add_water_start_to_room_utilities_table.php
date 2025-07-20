<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWaterStartToRoomUtilitiesTable extends Migration
{
    public function up()
    {
        Schema::table('room_utilities', function (Blueprint $table) {
            $table->integer('water_start')->nullable()->after('water_occupants');
        });
    }

    public function down()
    {
        Schema::table('room_utilities', function (Blueprint $table) {
            $table->dropColumn('water_start');
        });
    }
}