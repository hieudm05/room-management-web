<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFullNameToUserInfosTable extends Migration
{
    public function up()
    {
        Schema::table('user_infos', function (Blueprint $table) {
            $table->string('full_name')->nullable()->after('id'); // hoặc sau cột nào bạn muốn
        });
    }

    public function down()
    {
        Schema::table('user_infos', function (Blueprint $table) {
            $table->dropColumn('full_name');
        });
    }
}
