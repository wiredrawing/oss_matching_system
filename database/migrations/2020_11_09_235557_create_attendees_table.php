<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendees', function (Blueprint $table) {
            $table->string("room_id", 512);
            $table->bigInteger("member_id");
            $table->dateTime("created_at")->nullable();
            $table->dateTime("updated_at")->nullable();

            // 複合ユニークキー
            $table->unique([
                "room_id",
                "member_id",
            ], "room_id_member_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendees');
    }
}
