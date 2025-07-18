<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            // Chỉ thêm cột nếu chưa tồn tại
            if (!Schema::hasColumn('rooms', 'people_renter')) {
                $table->integer('people_renter')->default(0)->after('id_rental_agreements');
            }
        });
    }
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            // Chỉ xoá cột nếu đang tồn tại
            if (Schema::hasColumn('rooms', 'people_renter')) {
                $table->dropColumn('people_renter');
            }
        });
    }
};
