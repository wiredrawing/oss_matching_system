<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            // ルームテーブルのプライマリキーはランダムな文字列で定義
            // 使用可能文字 => a-z,A-Z,0-9,_,$,!,- とする
            $table->string("id", 512)->primary()->unique();
            // 参加女性ID
            $table->bigInteger("female_member_id")->nullable();
            // 参加男性ID
            $table->bigInteger("male_member_id")->nullable();
            $table->dateTime("created_at")->nullable();
            $table->dateTime("updated_at")->nullable();
            // 複合ユニークキー
            $table->unique(
                [
                    "female_member_id",
                    "male_member_id"
                ],
                "female_member_id_male_member_id"
            );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rooms');
    }
}
