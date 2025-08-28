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
        Schema::table('room_utility_photos', function (Blueprint $table) {
            $table->unsignedBigInteger('room_utility_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_utility_photos', function (Blueprint $table) {
            $table->unsignedBigInteger('room_utility_id')->nullable(false)->change();
        });
    }
};
