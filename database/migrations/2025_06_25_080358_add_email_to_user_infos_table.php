<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmailToUserInfosTable extends Migration
{
    public function up()
    {
        Schema::table('user_infos', function (Blueprint $table) {
            $table->string('email')->nullable()->after('phone');
        });
    }

    public function down()
    {
        Schema::table('user_infos', function (Blueprint $table) {
            $table->dropColumn('email');
        });
    }
}
