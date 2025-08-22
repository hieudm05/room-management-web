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
        Schema::table('deposit_refunds', function (Blueprint $table) {
            $table->enum('status', [
                'pending',       // Chờ xử lý
                'completed',     // Đã hoàn
                'not_refunded',  // Không hoànphp 
                'refunded'       // Đã hoàn (nếu muốn tách riêng completed/ refunded)
            ])->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deposit_refunds', function (Blueprint $table) {
            //
        });
    }
};
