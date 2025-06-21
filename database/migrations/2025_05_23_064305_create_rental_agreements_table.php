<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rental_agreements', function (Blueprint $table) {
            $table->bigIncrements('rental_id'); // 👈 phải có dòng này
            $table->unsignedBigInteger('room_id');
            $table->unsignedBigInteger('renter_id');

            $table->enum('status', ['Pending', 'Active', 'Signed', 'Terminated', 'Expired'])->default('Pending');



            $table->date('start_date');
            $table->date('end_date');
            $table->string('contract_file')->nullable();
            $table->timestamps();

            // Nếu cần khóa ngoại:
            // $table->foreign('room_id')->references('room_id')->on('rooms')->onDelete('cascade');
            // $table->foreign('renter_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_agreements');
    }
};
