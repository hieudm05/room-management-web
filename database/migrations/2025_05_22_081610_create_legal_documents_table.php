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
       Schema::create('legal_documents', function (Blueprint $table) {
            $table->bigIncrements('document_id'); // ID chính

            $table->unsignedBigInteger('user_id');      // Chủ trọ
            $table->unsignedBigInteger('property_id');  // Bất động sản liên quan

            $table->string('document_type', 100);       // Loại giấy tờ (Sổ đỏ, PCCC...)
            $table->string('file_path', 255);           // Đường dẫn đến file

            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending'); // Trạng thái duyệt

            $table->unsignedBigInteger('verified_by')->nullable(); // Người duyệt (Admin)
            $table->dateTime('uploaded_at');           // Ngày tải lên
            $table->dateTime('reviewed_at')->nullable(); // Ngày duyệt

            // Khóa ngoại
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
            $table->foreign('verified_by')->references('id')->on('users')->nullOnDelete();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legal_documents');
    }
};
