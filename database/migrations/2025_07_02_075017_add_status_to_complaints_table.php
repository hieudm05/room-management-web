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
            $table->enum('status', [
                'pending',       // chờ xử lý
                'in_progress',   // đang xử lý
                'resolved',      // đã xử lý
                'rejected',      // từ chối
                'cancelled'      // đã hủy
            ])->default('pending')->after('detail');
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            //
        });
    }
};
