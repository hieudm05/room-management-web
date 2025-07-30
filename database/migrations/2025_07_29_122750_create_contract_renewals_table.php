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
        Schema::create('contract_renewals', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('room_id');
    $table->unsignedBigInteger('user_id'); // người thuê yêu cầu
    $table->enum('status', ['pending', 'rejected'])->default('pending');
    $table->timestamps();

    $table->foreign('room_id')->references('room_id')->on('rooms')->onDelete('cascade');
    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_renewals');
    }
};
