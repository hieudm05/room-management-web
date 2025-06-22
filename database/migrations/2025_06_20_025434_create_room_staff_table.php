<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('room_staff', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('room_id'); // ✅ Bổ sung kiểu dữ liệu đúng
            $table->unsignedBigInteger('staff_id');
            $table->timestamps(); // để biết khi nào được phân công/quản lý

            $table->unique(['room_id', 'staff_id']); //

            $table->foreign('room_id')->references('room_id')->on('rooms')->onDelete('cascade');
            $table->foreign('staff_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_staff');
    }
};
