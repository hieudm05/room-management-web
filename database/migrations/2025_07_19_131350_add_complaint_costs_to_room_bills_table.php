<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
   public function up()
{
    Schema::table('room_bills', function (Blueprint $table) {
        if (!Schema::hasColumn('room_bills', 'complaint_user_cost')) {
            $table->decimal('complaint_user_cost', 15, 2)->default(0)->after('total'); // hoặc after('additional_fees_total') nếu tồn tại
        }
        if (!Schema::hasColumn('room_bills', 'complaint_landlord_cost')) {
            $table->decimal('complaint_landlord_cost', 15, 2)->default(0)->after('complaint_user_cost');
        }
    });
}


    public function down(): void
    {
        Schema::table('room_bills', function (Blueprint $table) {
            $table->dropColumn(['complaint_user_cost', 'complaint_landlord_cost']);
        });
    }
};

