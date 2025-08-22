<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rental_agreements', function (Blueprint $table) {
            $table->string('full_name')->nullable()->after('created_by');
            $table->string('email')->nullable()->after('full_name');
            $table->string('phone')->nullable()->after('email');
            $table->string('cccd')->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('rental_agreements', function (Blueprint $table) {
            $table->dropColumn(['full_name', 'email', 'phone', 'cccd']);
        });
    }
};
