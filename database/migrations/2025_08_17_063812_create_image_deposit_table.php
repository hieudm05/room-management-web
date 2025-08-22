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
        Schema::create('image_deposit', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('room_id');
            $table->unsignedBigInteger('rental_id');
            $table->string('image_url');
            $table->timestamps();

            // liên kết room và rental
            $table->foreign('room_id')->references('room_id')->on('rooms')->onDelete('cascade');
            $table->foreign('rental_id')->references('rental_id')->on('rental_agreements')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('image_deposit');
    }
};
