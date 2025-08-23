<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
    {
        DB::statement("ALTER TABLE room_leave_requests 
            MODIFY COLUMN action_type 
            ENUM('transfer', 'terminate', 'leave', 'leave_all') 
            CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'leave'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE room_leave_requests 
            MODIFY COLUMN action_type 
            ENUM('transfer', 'terminate', 'leave') 
            CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'leave'");
    }
};
