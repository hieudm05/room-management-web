<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('approvals', function (Blueprint $table) {
            $table->unsignedBigInteger('landlord_id')->after('room_id');

            // Nếu muốn có liên kết với bảng users hoặc landlords:
            $table->foreign('landlord_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('approvals', function (Blueprint $table) {
            $table->dropForeign(['landlord_id']);
            $table->dropColumn('landlord_id');
        });
    }
};
