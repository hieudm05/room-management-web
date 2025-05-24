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
        Schema::create('room_photos', function (Blueprint $table) {
            $table->id('photo_id'); // Khóa chính tự tăng
            $table->unsignedBigInteger('room_id'); // FK
            $table->string('image_url', 255); // URL ảnh
            $table->timestamps();

            // Ràng buộc khóa ngoại
            $table->foreign('room_id')
                  ->references('room_id')
                  ->on('rooms')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_photos');
    }
};
