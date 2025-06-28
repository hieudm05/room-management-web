<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('room_bills', function (Blueprint $table) {
          DB::statement("ALTER TABLE room_bills MODIFY status ENUM('unpaid', 'pending', 'paid') DEFAULT 'unpaid'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_bills', function (Blueprint $table) {
              DB::statement("ALTER TABLE room_bills MODIFY status ENUM('unpaid', 'paid') DEFAULT 'unpaid'");
        });
    }
};
