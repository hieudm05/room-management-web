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
        Schema::table('approvals', function (Blueprint $table) {
            // Kiểm tra nếu cột tồn tại thì mới thêm khóa ngoại
            if (Schema::hasColumn('approvals', 'rental_id')) {
                $table->foreign('rental_id')
                    ->references('rental_id')
                    ->on('rental_agreements')
                    ->onDelete('set null'); // hoặc cascade tùy logic bạn muốn
            }
        });
    }

    public function down(): void
    {
        Schema::table('approvals', function (Blueprint $table) {
            $table->dropForeign(['rental_id']);
        });
    }
};
