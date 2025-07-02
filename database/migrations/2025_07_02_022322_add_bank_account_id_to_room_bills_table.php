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
        Schema::table('room_bills', function (Blueprint $table) {
            $table->unsignedBigInteger('bank_account_id')->nullable()->after('room_id');

            $table->foreign('bank_account_id')
                ->references('id')
                ->on('bank_accounts')
                ->onDelete('set null');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_bills', function (Blueprint $table) {
            //
        });
    }
};
