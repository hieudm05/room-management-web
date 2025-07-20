<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_posts', function (Blueprint $table) {
            $table->id('post_id');
            $table->foreignId('staff_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('landlord_id')->constrained('users')->onDelete('cascade');

            // Không dùng constrained vì properties không có id
            $table->unsignedBigInteger('property_id');
            $table->foreign('property_id')->references('property_id')->on('properties')->onDelete('cascade');

            // Bổ sung loại chuyên mục
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('category_id')->on('categories')->onDelete('cascade');


            $table->string('title');
            $table->string('slug')->unique();
            $table->string('price');
            $table->integer('area');
            $table->string('address');
            $table->string('district');
            $table->string('ward');
            $table->string('city');
            $table->dateTime('published_at')->nullable();
            $table->dateTime('expired_at')->nullable();
            $table->text('description')->nullable();

            // Giữ nguyên nếu muốn: tiện ích, nội thất
            $table->json('amenities')->nullable();
            $table->json('furnitures')->nullable();

            $table->string('suitability')->nullable();
            $table->string('contract_term')->nullable();
            $table->string('post_code')->nullable();
            $table->string('thumbnail')->nullable();
            $table->json('gallery')->nullable();

            $table->unsignedTinyInteger('status')->default(0); // 0: pending, 1: approved, 2: rejected

            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejected_reason')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_posts');
    }
};
