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
        Schema::table('approvals', function (Blueprint $table) {
            if (!Schema::hasColumn('approvals', 'rental_id')) {
                $table->unsignedBigInteger('rental_id')->nullable()->after('staff_id');
            }
        });
    }

    public function down()
    {
        Schema::table('approvals', function (Blueprint $table) {
            $table->dropForeign(['rental_id']);
            $table->dropColumn('rental_id');
        });
    }
};
