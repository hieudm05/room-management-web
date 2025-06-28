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
        if (!Schema::hasColumn('rooms', 'id_rental_agreements')) {
            $table->unsignedBigInteger('id_rental_agreements')->nullable()->after('status');
        }
    });
}


    /**
     * Reverse the migrations.
     */
public function down(): void
{
    Schema::table('rooms', function (Blueprint $table) {
        if (Schema::hasColumn('rooms', 'id_rental_agreements')) {
            $table->dropColumn('id_rental_agreements');
        }
    });
}

};
