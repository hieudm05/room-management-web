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
        Schema::table('rooms', function (Blueprint $table) {
        $table->unsignedInteger('price_edit_count')->default(0);
        $table->unsignedInteger('deposit_edit_count')->default(0);
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
        $table->dropColumn(['price_edit_count', 'deposit_edit_count']);
    });
    }
};
