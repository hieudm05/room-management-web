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
                // Kiểm tra nếu cột 'id_rental_agreements' tồn tại thì thêm sau nó,
                // nếu không thì thêm bình thường
                if (Schema::hasColumn('rooms', 'id_rental_agreements')) {
                    $table->integer('people_renter')->default(0)->after('id_rental_agreements');
                } else {
                    $table->integer('people_renter')->default(0);
                }
            }
        });
    }
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            // Chỉ xóa nếu cột tồn tại
            if (Schema::hasColumn('rooms', 'people_renter')) {
                $table->dropColumn('people_renter');
            }
        });
    }
};
