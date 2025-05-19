<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id(); // property_id (PK, AI)
            $table->unsignedBigInteger('landlord_id'); // FK
            $table->string('name', 100);
            $table->text('address');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['Pending', 'Approved', 'Rejected', 'Suspended'])->default('Pending');
            $table->timestamps(); // created_at & updated_at

            // Khóa ngoại
            $table->foreign('landlord_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
