<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('rental_agreements', function (Blueprint $table) {
        if (!Schema::hasColumn('rental_agreements', 'deposit_id')) {
            $table->unsignedBigInteger('deposit_id')->nullable()->after('room_id');
            $table->foreign('deposit_id')
                  ->references('id')->on('image_deposit')
                  ->onDelete('cascade');
        }
    });
}
    public function down(): void
    {
        Schema::table('rental_agreements', function (Blueprint $table) {
            $table->dropForeign(['deposit_id']);
            $table->dropColumn('deposit_id');
        });
    }
};
