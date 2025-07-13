<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_property_id_to_legal_documents_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('legal_documents', function (Blueprint $table) {
            $table->unsignedBigInteger('property_id')->nullable()->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('legal_documents', function (Blueprint $table) {
            $table->dropColumn('property_id');
        });
    }
};