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
        Schema::create('room_services', function (Blueprint $table) {
            $table->unsignedBigInteger('room_id');
            $table->unsignedBigInteger('service_id');
            $table->boolean('is_free')->default(true);
            $table->decimal('price', 10, 2)->nullable();

            $table->foreign('room_id')->references('room_id')->on('rooms')->onDelete('cascade');
            $table->foreign('service_id')->references('service_id')->on('services')->onDelete('cascade');

            $table->primary(['room_id', 'service_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_services');
    }
};
