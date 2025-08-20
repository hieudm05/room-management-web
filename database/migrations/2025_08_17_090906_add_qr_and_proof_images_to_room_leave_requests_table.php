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
            $table->string('deposit_qr_image')->nullable()->after('note'); // Ảnh QR người thuê gửi
            $table->string('proof_image')->nullable()->after('deposit_qr_image'); // Ảnh minh chứng chủ nhà
        });
    }

    public function down()
    {
        Schema::table('room_leave_requests', function (Blueprint $table) {
            $table->dropColumn(['deposit_qr_image', 'proof_image']);
        });
    }
};
