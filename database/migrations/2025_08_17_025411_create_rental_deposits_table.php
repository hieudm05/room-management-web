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
        Schema::create('deposit_refunds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rental_id');   // hợp đồng liên quan
            $table->unsignedBigInteger('user_id');     // người được hoàn cọc (chủ hợp đồng cũ)
            $table->decimal('amount', 12, 2);          // số tiền cọc
            $table->date('refund_date');               // ngày landlord xác nhận hoàn
            $table->enum('status', ['pending', 'completed'])->default('pending'); 
            $table->timestamps();

            // Quan hệ khóa ngoại
            $table->foreign('rental_id')
                  ->references('rental_id')
                  ->on('rental_agreements')
                  ->onDelete('cascade');

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
        Schema::dropIfExists('deposit_refunds');
    }
};