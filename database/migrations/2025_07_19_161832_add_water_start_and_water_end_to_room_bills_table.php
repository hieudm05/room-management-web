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
        Schema::table('room_bills', function (Blueprint $table) {
            //
            Schema::table('room_bills', function (Blueprint $table) {
    $table->integer('water_start')->nullable()->after('water_occupants');
    $table->integer('water_end')->nullable()->after('water_start');
});

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_bills', function (Blueprint $table) {
            //
        });
    }
};
