<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // chủ trọ
            $table->string('bank_name');          // Tên ngân hàng
            $table->string('bank_account_name');  // Tên chủ tài khoản
            $table->string('bank_account_number'); // Số tài khoản
            $table->timestamps();

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade'); // Xóa user => xóa ngân hàng
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};
