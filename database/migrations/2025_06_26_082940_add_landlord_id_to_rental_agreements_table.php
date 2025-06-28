<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('rental_agreements', 'landlord_id')) {
            Schema::table('rental_agreements', function (Blueprint $table) {
                $table->unsignedBigInteger('landlord_id')->nullable()->after('end_date');
            });
        }
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rental_agreements', function (Blueprint $table) {
            if (Schema::hasColumn('rental_agreements', 'landlord_id')) {
                // Xóa khóa ngoại nếu tồn tại
                DB::statement('ALTER TABLE rental_agreements DROP FOREIGN KEY IF EXISTS rental_agreements_landlord_id_foreign');

                // Xóa cột nếu có
                $table->dropColumn('landlord_id');
            }
        });
    }
};
