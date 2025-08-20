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
       // database/migrations/xxxx_add_room_bill_id_to_complaints_table.php
        Schema::table('complaints', function (Blueprint $table) {
            $table->foreignId('room_bill_id')->nullable()->constrained('room_bills')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            //
        });
    }
};
