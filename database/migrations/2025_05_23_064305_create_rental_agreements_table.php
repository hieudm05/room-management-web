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
    Schema::create('rental_agreements', function (Blueprint $table) {
        $table->id('rental_id');
        $table->unsignedBigInteger('room_id');
        $table->unsignedBigInteger('renter_id')->nullable(); // bạn có thể tạo bảng renters sau
        $table->unsignedBigInteger('landlord_id')->nullable(); // nếu cần
        $table->date('start_date');
        $table->date('end_date');
        $table->decimal('rental_price', 12, 2);
        $table->decimal('deposit', 12, 2);
        $table->enum('status', ['Pending', 'Signed', 'Active', 'Terminated', 'Expired', 'Rejected'])->default('Pending');
        $table->string('contract_file')->nullable();
        $table->text('agreement_terms')->nullable();
        $table->unsignedBigInteger('created_by')->nullable();
        $table->timestamps();

        $table->foreign('room_id')->references('room_id')->on('rooms')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_agreements');
    }
};
