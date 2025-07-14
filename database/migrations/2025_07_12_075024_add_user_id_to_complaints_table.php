<?php

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
    Schema::table('complaints', function (Blueprint $table) {
        // Không thêm cột nữa — chỉ gắn foreign key nếu cần
        $table->foreign('user_id')
              ->references('id')
              ->on('users')
              ->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
{
    Schema::table('complaints', function (Blueprint $table) {
        $table->dropForeign(['user_id']);
    });
}

};