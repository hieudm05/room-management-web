<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('features', function (Blueprint $table) {
            $table->id('feature_id');
            $table->string('name');
            $table->timestamps();
        });

        // Seed các đặc điểm nổi bật
        DB::table('features')->insert([
            ['name' => 'Đầy đủ nội thất'],
            ['name' => 'Có gác'],
            ['name' => 'Có kệ bếp'],
            ['name' => 'Có máy lạnh'],
            ['name' => 'Có máy giặt'],
            ['name' => 'Có tủ lạnh'],
            ['name' => 'Có thang máy'],
            ['name' => 'Không chung chủ'],
            ['name' => 'Giờ giấc tự do'],
            ['name' => 'Có bảo vệ 24/24'],
            ['name' => 'Có hầm để xe'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('features');
    }
};
