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
    Schema::table('room_leave_requests', function (Blueprint $table) {
        $table->enum('action_type', ['transfer', 'terminate', 'leave'])->default('leave')->after('leave_date');
    });
}

public function down()
{
    Schema::table('room_leave_requests', function (Blueprint $table) {
        $table->dropColumn('action_type');
    });
}
};
