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
        Schema::table('image_deposit', function (Blueprint $table) {
            $table->unsignedBigInteger('rental_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('image_deposit', function (Blueprint $table) {
            $table->unsignedBigInteger('rental_id')->nullable(false)->change();
        });
    }
};
