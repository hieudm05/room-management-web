<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('rental_agreements', function (Blueprint $table) {
            $table->longText('contract_text')->nullable();
        });
    }

    public function down()
    {
        Schema::table('rental_agreements', function (Blueprint $table) {
            $table->dropColumn('contract_text');
        });
    }
};
