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
        if (!Schema::hasTable('bill_services')) {
            Schema::create('bill_services', function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('bill_id');
                $table->unsignedBigInteger('service_id')->nullable(); // nullable nếu muốn lưu cả dịch vụ không tồn tại trong bảng chính

                $table->string('name');
                $table->decimal('price', 12, 2)->nullable();
                $table->integer('qty');
                $table->decimal('total', 12, 2);
                $table->string('type_display'); // 'Miễn phí', 'Tính theo người',...

                $table->timestamps();

                // Foreign keys
                $table->foreign('bill_id')->references('id')->on('room_bills')->onDelete('cascade');
                $table->foreign('service_id')->references('service_id')->on('services')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_services');
    }
};
