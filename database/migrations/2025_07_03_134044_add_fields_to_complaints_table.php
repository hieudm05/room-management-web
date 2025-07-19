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
    $table->unsignedBigInteger('staff_id')->nullable()->after('common_issue_id');
    $table->decimal('user_cost', 15, 2)->nullable()->after('status');
    $table->decimal('landlord_cost', 15, 2)->nullable()->after('user_cost');
    $table->text('note')->nullable()->after('landlord_cost');
    $table->timestamp('resolved_at')->nullable()->after('updated_at');

    $table->foreign('staff_id')->references('id')->on('users')->nullOnDelete();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            //
        });
    }
};
