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
        Schema::table('room_leave_logs', function (Blueprint $table) {
            $table->string('action_type', 255)->change();
        });
    }

    public function down()
    {
        Schema::table('room_leave_logs', function (Blueprint $table) {
            $table->string('action_type', 10)->change(); // revert lại độ dài cũ nếu biết
        });
    }
};

