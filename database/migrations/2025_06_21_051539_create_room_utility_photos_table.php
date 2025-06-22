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
        Schema::create('room_utility_photos', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('room_utility_id');
        $table->string('image_path'); // lưu tên file hoặc path
        $table->timestamps();

        $table->foreign('room_utility_id')
            ->references('id')
            ->on('room_utilities')
            ->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_utility_photos');
    }
};
