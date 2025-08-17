<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyStaffIdNullableOnStaffPostsTable extends Migration
{
    public function up()
    {
        Schema::table('staff_posts', function (Blueprint $table) {
            $table->unsignedBigInteger('staff_id')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('staff_posts', function (Blueprint $table) {
            $table->unsignedBigInteger('staff_id')->nullable(false)->change();
        });
    }
}
