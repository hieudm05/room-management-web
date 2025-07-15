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
    Schema::table('complaint_photos', function (Blueprint $table) {
        $table->string('type')->default('before')->after('photo_path'); // 'before' hoặc 'resolved'
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complaint_photos', function (Blueprint $table) {
            //
        });
    }
};
