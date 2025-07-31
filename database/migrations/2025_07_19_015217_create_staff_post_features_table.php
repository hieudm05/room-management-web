<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
      Schema::create('staff_post_features', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('post_id');
    $table->unsignedBigInteger('feature_id');

    // Đúng khóa chính là post_id và feature_id
    $table->foreign('post_id')->references('post_id')->on('staff_posts')->onDelete('cascade');
    $table->foreign('feature_id')->references('feature_id')->on('features')->onDelete('cascade');
});


    }

    public function down(): void
    {
        Schema::dropIfExists('staff_post_features');
    }
};
