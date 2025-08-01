<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
      Schema::create('room_leave_logs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');               // Người gửi yêu cầu rời phòng
            $table->unsignedBigInteger('room_id');               // Phòng liên quan
            $table->unsignedBigInteger('rental_id')->nullable(); // Hợp đồng tại thời điểm đó (nếu có)

            $table->date('leave_date');                           // Ngày rời đi
            $table->enum('action_type', ['terminate', 'transfer']);

            $table->unsignedBigInteger('previous_renter_id')->nullable(); // Chủ hợp đồng trước (nếu là chuyển nhượng)
            $table->unsignedBigInteger('new_renter_id')->nullable();      // Chủ hợp đồng mới (nếu có)

            $table->text('reason')->nullable();                  // Lý do
            $table->enum('status', ['Approved', 'Rejected', 'Cancelled']);

            $table->unsignedBigInteger('handled_by')->nullable(); // Người duyệt yêu cầu (landlord/staff)

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('room_id')->references('room_id')->on('rooms')->onDelete('cascade');
            $table->foreign('rental_id')->references('rental_id')->on('rental_agreements')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_leave_logs');
    }
};
