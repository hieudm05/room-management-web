<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('room_staff', function (Blueprint $table) {
            $table->enum('status', ['active', 'inactive'])->default('active')->after('staff_id');
        });
    }
    public function down(): void
    {
        Schema::table('room_staff', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
