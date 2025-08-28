<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('user_infos', function (Blueprint $table) {
            // Bỏ ràng buộc cũ
            $table->dropForeign(['user_id']);
            $table->dropUnique('user_infos_user_id_unique');

            // Thêm ràng buộc mới
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->unique(['user_id', 'rental_id'], 'user_infos_user_rental_unique');
        });
    }

    public function down(): void
    {
        Schema::table('user_infos', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropUnique('user_infos_user_rental_unique');

            $table->unique('user_id', 'user_infos_user_id_unique');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }
};