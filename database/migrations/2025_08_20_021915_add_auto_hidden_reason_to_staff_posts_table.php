<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('staff_posts', function (Blueprint $table) {
            $table->string('auto_hidden_reason')->nullable()->after('is_public')
                ->comment('Lý do auto ẩn bài đăng, ví dụ: phòng full người');
        });
    }

    public function down(): void
    {
        Schema::table('staff_posts', function (Blueprint $table) {
            $table->dropColumn('auto_hidden_reason');
        });
    }
};
