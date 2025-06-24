<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRentalPriceAndDepositToApprovalsTable extends Migration
{
    public function up()
    {
        Schema::table('approvals', function (Blueprint $table) {
            $table->decimal('rental_price', 15, 2)->nullable()->after('note');
            $table->decimal('deposit', 15, 2)->nullable()->after('rental_price');
        });
    }

    public function down()
    {
        Schema::table('approvals', function (Blueprint $table) {
            $table->dropColumn(['rental_price', 'deposit']);
        });
    }
}
