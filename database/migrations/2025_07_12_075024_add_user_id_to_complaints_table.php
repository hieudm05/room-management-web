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
         $table->unsignedBigInteger('user_id')->after('id'); // hoặc after một cột khác
        $table->foreign('user_id')
              ->references('id')
              ->on('users')
              ->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
     public function down()
    {
        Schema::table('complaints', function (Blueprint $table) {
            // Xóa foreign key trước
            $table->dropForeign(['user_id']);

            // Xóa cột
            $table->dropColumn('user_id');
        });
    }

};
