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
            $table->string('proof_image')->nullable()->after('status');
            $table->text('reason')->nullable()->after('proof_image');
        });
    }

    public function down(): void
    {
        Schema::table('deposit_refunds', function (Blueprint $table) {
            $table->dropColumn(['proof_image', 'reason']);
        });
    }
};
