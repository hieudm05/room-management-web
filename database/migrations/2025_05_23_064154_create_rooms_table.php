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
    Schema::create('rooms', function (Blueprint $table) {
        $table->id('room_id');
        $table->unsignedBigInteger('property_id');
        $table->string('room_number', 50);
        $table->decimal('rental_price', 12, 2);
        $table->float('area');
        $table->enum('status', ['Available', 'Rented', 'Hidden', 'Suspended', 'Confirmed'])->default('Available');
        $table->timestamps();

        $table->foreign('property_id')->references('property_id')->on('properties')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
