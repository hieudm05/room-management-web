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
    Schema::create('room_facilities', function (Blueprint $table) {
        $table->unsignedBigInteger('room_id');
        $table->unsignedBigInteger('facility_id');

        $table->foreign('room_id')->references('room_id')->on('rooms')->onDelete('cascade');
        $table->foreign('facility_id')->references('facility_id')->on('facilities')->onDelete('cascade');

        $table->primary(['room_id', 'facility_id']);
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_facilities');
    }
};
