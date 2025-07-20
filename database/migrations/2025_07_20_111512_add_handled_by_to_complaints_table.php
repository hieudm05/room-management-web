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
    Schema::table('complaints', function (Blueprint $table) {
        $table->unsignedBigInteger('handled_by')->nullable()->after('status');

        $table->foreign('handled_by')->references('id')->on('users')->onDelete('set null');
    });
}

public function down()
{
    Schema::table('complaints', function (Blueprint $table) {
        $table->dropForeign(['handled_by']);
        $table->dropColumn('handled_by');
    });
}
};
