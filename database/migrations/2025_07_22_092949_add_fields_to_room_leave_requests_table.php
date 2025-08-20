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
            $table->unsignedBigInteger('handled_by')->nullable()->after('status');
            $table->timestamp('handled_at')->nullable()->after('handled_by');
            $table->unsignedBigInteger('new_renter_id')->nullable()->after('user_id');
             $table->text('reject_reason')->nullable();

            // Nếu muốn tạo liên kết khóa ngoại (khuyến nghị):
            $table->foreign('new_renter_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('handled_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('room_leave_requests', function (Blueprint $table) {
            $table->dropForeign(['new_renter_id']);
            $table->dropForeign(['handled_by']);
            $table->dropColumn(['handled_by', 'handled_at', 'new_renter_id']);
        });
    }
};
