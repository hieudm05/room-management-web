<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('staff_posts', function (Blueprint $table) {
            $table->boolean('is_public')->default(false)->after('status');
        });
    }

    public function down()
    {
        Schema::table('staff_posts', function (Blueprint $table) {
            $table->dropColumn('is_public');
        });
    }
};
