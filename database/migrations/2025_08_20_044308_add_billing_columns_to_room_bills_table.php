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
        Schema::table('room_bills', function (Blueprint $table) {
            if (!Schema::hasColumn('room_bills', 'original_rent_price')) {
                $table->decimal('original_rent_price', 15, 2)->nullable()->after('rent_price')
                    ->comment('Giá phòng gốc trước khi tính theo tỷ lệ');
            }

            if (!Schema::hasColumn('room_bills', 'billing_days')) {
                $table->integer('billing_days')->nullable()->after('original_rent_price')
                    ->comment('Số ngày tính tiền trong tháng');
            }

            if (!Schema::hasColumn('room_bills', 'total_days_in_month')) {
                $table->integer('total_days_in_month')->nullable()->after('billing_days')
                    ->comment('Tổng số ngày trong tháng');
            }

            if (!Schema::hasColumn('room_bills', 'billing_ratio')) {
                $table->decimal('billing_ratio', 5, 4)->nullable()->default(1.0)->after('total_days_in_month')
                    ->comment('Tỷ lệ tính tiền (billing_days/total_days_in_month)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_bills', function (Blueprint $table) {
            if (Schema::hasColumn('room_bills', 'original_rent_price')) {
                $table->dropColumn('original_rent_price');
            }
            if (Schema::hasColumn('room_bills', 'billing_days')) {
                $table->dropColumn('billing_days');
            }
            if (Schema::hasColumn('room_bills', 'total_days_in_month')) {
                $table->dropColumn('total_days_in_month');
            }
            if (Schema::hasColumn('room_bills', 'billing_ratio')) {
                $table->dropColumn('billing_ratio');
            }
        });
    }
};
