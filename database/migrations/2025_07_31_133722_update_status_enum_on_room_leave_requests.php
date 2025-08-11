<?php
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    DB::statement("ALTER TABLE room_leave_requests 
        MODIFY status ENUM('penfding', 'staff_approved', 'waiting_new_renter_accept', 'approved', 'rejected')");
}

public function down()
{
    DB::statement("ALTER TABLE room_leave_requests 
        MODIFY status ENUM('pending', 'staff_approved', 'approved', 'rejected')");
}
};
