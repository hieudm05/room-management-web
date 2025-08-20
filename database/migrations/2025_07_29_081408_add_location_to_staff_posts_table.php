<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLocationToStaffPostsTable extends Migration
{
    public function up()
    {
        Schema::table('staff_posts', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
        });
    }

    public function down()
    {
        Schema::table('staff_posts', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });
    }
}
