<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomUtilitiesTable extends Migration
{
    public function up()
    {
        Schema::create('room_utilities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('room_id');

            // Date range
            $table->date('start_date');
            $table->date('end_date');

            // Electricity
            $table->integer('electric_start')->nullable();
            $table->integer('electric_end')->nullable();
            $table->integer('electric_kwh')->nullable();
            $table->bigInteger('electricity')->default(0);

            // Water
            $table->enum('water_unit', ['per_person', 'per_m3'])->default('per_m3');
            $table->integer('water_occupants')->nullable();
            $table->decimal('water_m3', 8, 2)->nullable();
            $table->bigInteger('water')->default(0);

            // Uploaded images
            $table->json('images')->nullable();

            $table->timestamps();

            $table->foreign('room_id')->references('room_id')->on('rooms')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('room_utilities');
    }
}

