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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            // Liên kết với staff_posts.post_id
            $table->unsignedBigInteger('post_id');
            $table->foreign('post_id')->references('post_id')->on('staff_posts')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->dateTime('check_in');
            $table->string('note')->nullable();

            $table->enum('status', ['pending', 'approved', 'rejected', 'waiting', 'no-cancel', 'completed'])->default('pending');

            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();

            $table->string('proof_image')->nullable();
            $table->string('guest_name')->nullable();
            $table->string('phone', 20)->nullable();

            $table->unsignedBigInteger('room_id')->nullable();


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
