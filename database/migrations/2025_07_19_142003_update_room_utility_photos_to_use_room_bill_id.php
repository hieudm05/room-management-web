<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateRoomUtilityPhotosToUseRoomBillId extends Migration
{
 public function up()
{
   if (!Schema::hasColumn('room_utility_photos', 'room_bill_id')) {
    Schema::table('room_utility_photos', function (Blueprint $table) {
        $table->unsignedBigInteger('room_bill_id')->nullable()->after('id');
    });
}

}

    public function down()
    {
        Schema::table('room_utility_photos', function (Blueprint $table) {
            $table->dropForeign(['room_bill_id']);
            $table->dropColumn('room_bill_id');

            $table->unsignedBigInteger('room_utility_id');
            $table->foreign('room_utility_id')->references('id')->on('room_utilities')->onDelete('cascade');
        });
    }
}

