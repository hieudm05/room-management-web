<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rental_agreements', function (Blueprint $table) {
            $table->decimal('deposit', 12, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('rental_agreements', function (Blueprint $table) {
            $table->dropColumn('deposit');
        });
    }
};
