<?php
// database/migrations/xxxx_xx_xx_add_landlord_id_to_users_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLandlordIdToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('landlord_id')->nullable()->after('id');
            // Nếu muốn, có thể thêm foreign key:
            // $table->foreign('landlord_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // $table->dropForeign(['landlord_id']);
            $table->dropColumn('landlord_id');
        });
    }
}