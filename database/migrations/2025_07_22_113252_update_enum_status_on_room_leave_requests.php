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
   public function up()
    {
        DB::statement("ALTER TABLE room_leave_requests 
            MODIFY COLUMN status ENUM('pending', 'staff_approved', 'approved', 'rejected', 'cancelled') 
            NOT NULL DEFAULT 'pending'");
    }

    public function down()
    {
        // Khôi phục ENUM cũ nếu cần rollback
        DB::statement("ALTER TABLE room_leave_requests 
            MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'cancelled') 
            NOT NULL DEFAULT 'pending'");
    }
};
