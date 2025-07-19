<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('bank_accounts', function ($table) {
            $table->string('bank_code', 10)->nullable()->after('bank_name');
        });
    }
    public function down()
    {
        Schema::table('bank_accounts', function ($table) {
            $table->dropColumn('bank_code');
        });
    }
};
