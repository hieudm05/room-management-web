<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->unsignedBigInteger('id_rental_agreements')->nullable()->after('status');
            // Nếu muốn tạo foreign key:
            // $table->foreign('id_rental_agreements')->references('id')->on('rental_agreements')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn('id_rental_agreements');
        });
    }
};
