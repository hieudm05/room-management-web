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
       Schema::table('user_infos', function (Blueprint $table) {
       $table->boolean('active')->default(true)->after('room_id');       // còn trong phòng?
       $table->timestamp('left_at')->nullable()->after('active');  
            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_infos', function (Blueprint $table) {
            //
        });
    }
};
