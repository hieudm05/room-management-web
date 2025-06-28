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
        Schema::create('room_bills', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('room_id');
            $table->date('month'); // YYYY-MM-01

            $table->string('tenant_name');
            $table->float('area');
            $table->decimal('rent_price', 10, 2);

            $table->integer('electric_start')->nullable();
            $table->integer('electric_end')->nullable();
            $table->integer('electric_kwh')->nullable();
            $table->decimal('electric_unit_price', 10, 2)->nullable();
            $table->decimal('electric_total', 10, 2)->nullable();

            $table->decimal('water_price', 10, 2)->nullable();
            $table->string('water_unit')->nullable(); // per_person, per_m3
            $table->integer('water_occupants')->nullable();
            $table->float('water_m3')->nullable();
            $table->decimal('water_total', 10, 2)->nullable();

            $table->decimal('total', 10, 2)->nullable();
            $table->string('status')->default('unpaid'); // unpaid, paid
            $table->foreign('room_id')->references('room_id')->on('rooms')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_bills');
    }
};
