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
         Schema::table('complaints', function (Blueprint $table) {
            $table->string('main_photo')->nullable()->after('detail'); // ảnh chính
            $table->json('photo_album')->nullable()->after('main_photo'); // nhiều ảnh
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropColumn(['main_photo', 'photo_album']);
        });
    }
};
