<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('approvals', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('room_id');  // Khóa ngoại đến rooms
            $table->unsignedBigInteger('staff_id'); // Khóa ngoại đến users

            $table->string('type'); // contract_upload, document_upload, etc.
            $table->string('file_path')->nullable(); // Đường dẫn file
            $table->text('note')->nullable(); // Ghi chú thêm
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            $table->timestamps();

            // Khóa ngoại
            $table->foreign('room_id')->references('room_id')->on('rooms')->onDelete('cascade');
            $table->foreign('staff_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approvals');
    }
};
