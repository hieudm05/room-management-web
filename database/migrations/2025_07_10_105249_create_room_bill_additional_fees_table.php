<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomBillAdditionalFeesTable extends Migration
{
    public function up()
    {
        Schema::create('room_bill_additional_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_bill_id')->constrained('room_bills')->onDelete('cascade');
            $table->string('name');
            $table->decimal('price', 15, 2);
            $table->integer('qty');
            $table->decimal('total', 15, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('room_bill_additional_fees');
    }
}