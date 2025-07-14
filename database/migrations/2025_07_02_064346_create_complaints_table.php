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
       Schema::create('complaints', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('property_id'); // ✅ Cột bắt buộc
    $table->unsignedBigInteger('room_id');
    $table->unsignedBigInteger('common_issue_id');
    $table->string('full_name');
    $table->string('phone');
    $table->text('detail')->nullable();
    
    $table->timestamps();

    // 🔗 Khóa ngoại
    $table->foreign('property_id')->references('property_id')->on('properties')->onDelete('cascade');
    $table->foreign('room_id')->references('room_id')->on('rooms')->onDelete('cascade');
    $table->foreign('common_issue_id')->references('id')->on('common_issues')->onDelete('cascade');
});
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
