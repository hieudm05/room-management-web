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
        Schema::table('room_staff', function (Blueprint $table) {
            if (!Schema::hasColumn('room_staff', 'status')) {
                $table->string('status')->default('active');
            }
        });
    }

    public function down(): void
    {
        Schema::table('room_staff', function (Blueprint $table) {
            if (Schema::hasColumn('room_staff', 'status')) {
                $table->dropColumn('status');
            }
        });
    }

  

};
