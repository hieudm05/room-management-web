<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('room_leave_requests', function (Blueprint $table) {
            $table->id(); // ID yêu cầu rời phòng

            $table->unsignedBigInteger('room_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('rental_agreement_id')->nullable();

            $table->unsignedBigInteger('staff_id')->nullable();     // Nhân viên xử lý
           $table->string('status')->default('pending');


            $table->unsignedBigInteger('landlord_id')->nullable();  // Chủ nhà duyệt cuối
            $table->enum('landlord_status', ['Pending', 'Approved', 'Rejected', 'Cancelled'])->default('Pending');

            $table->enum('status', ['Pending', 'Approved', 'Rejected', 'Cancelled'])->default('Pending'); // Tổng trạng thái

            $table->date('leave_date');       // Ngày muốn rời đi
            $table->text('note')->nullable(); // Lý do

            $table->timestamps();

            // Khóa ngoại
          $table->foreign('room_id')->references('room_id')->on('rooms')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
          $table->foreign('rental_agreement_id')->references('rental_id')->on('rental_agreements')->onDelete('set null');
            $table->foreign('staff_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('landlord_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_leave_requests');
    }
};
