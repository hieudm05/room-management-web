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
        Schema::table('room_leave_requests', function (Blueprint $table) {
            $table->string('status', 100)->change(); // hoặc 100 cho thoải mái
        });
    }

    public function down(): void
    {
        Schema::table('room_leave_requests', function (Blueprint $table) {
            $table->string('status', 20)->change(); // giả sử ban đầu là 20
        });
    }
};
