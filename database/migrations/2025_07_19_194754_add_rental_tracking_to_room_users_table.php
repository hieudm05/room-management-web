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
    Schema::table('room_users', function (Blueprint $table) {
        $table->dateTime('started_at')->nullable();
        $table->dateTime('stopped_at')->nullable();
        $table->boolean('is_active')->default(true);
        
        $table->integer('deposit_amount')->default(0);
        $table->integer('deduction_amount')->default(0);
        $table->integer('returned_amount')->default(0);
        $table->text('deduction_reason')->nullable();
        $table->string('deposit_status')->default('Đang giữ');
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_users', function (Blueprint $table) {
            //
        });
    }
};
