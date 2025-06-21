<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rental_agreements', function (Blueprint $table) {
            $table->unsignedBigInteger('landlord_id')->nullable()->after('renter_id');

            // Nếu có bảng users và muốn khóa ngoại:
            $table->foreign('landlord_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('rental_agreements', function (Blueprint $table) {
            $table->dropForeign(['landlord_id']);
            $table->dropColumn('landlord_id');
        });
    }
};
