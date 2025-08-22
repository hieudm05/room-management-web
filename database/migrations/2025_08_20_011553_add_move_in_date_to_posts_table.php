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
        Schema::table('staff_posts', function (Blueprint $table) {
            $table->date('move_in_date')->nullable()->after('expired_at');
        });
    }

    public function down()
    {
        Schema::table('staff_posts', function (Blueprint $table) {
            $table->dropColumn('move_in_date');
        });
    }
};
