<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('room_users', function (Blueprint $table) {
            if (!Schema::hasColumn('room_users', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('room_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('room_users', function (Blueprint $table) {
            if (Schema::hasColumn('room_users', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }
};
