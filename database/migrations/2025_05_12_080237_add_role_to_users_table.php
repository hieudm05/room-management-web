<?php

use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
     public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', [
                'Admin',
                'Renter',
                'Landlord',
                'Staff',
                'Manager',
            ])->default('Renter')->after('password');
        });
    }
    /**
     * Reverse the migrations.
     */
     public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
