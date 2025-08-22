<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staff_posts', function (Blueprint $table) {
            $table->unsignedTinyInteger('post_by') // Số nguyên nhỏ, 0-255
                ->default(0) // Ví dụ: 0 = staff, 1 = landlord
                ->after('longitude');
        });
    }

    public function down(): void
    {
        Schema::table('staff_posts', function (Blueprint $table) {
            $table->dropColumn('post_by');
        });
    }
};
