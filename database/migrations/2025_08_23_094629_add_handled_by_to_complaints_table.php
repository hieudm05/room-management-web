<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up(): void
{
    Schema::table('complaints', function (Blueprint $table) {
        $table->unsignedBigInteger('handled_by')->nullable()->after('resolved_at');
    });
}

public function down(): void
{
    Schema::table('complaints', function (Blueprint $table) {
        $table->dropColumn('handled_by');
    });
}

};
