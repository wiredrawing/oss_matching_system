<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            // 発言した会員ID
            $table->bigInteger("member_id")->unsigned();
            // 発言したメッセージ本文
            $table->string("message", 4096);
            $table->dateTime("created_at")->nullable();
            $table->dateTime("updated_at")->nullable();
            // 論理削除
            $table->softDeletes();

            // index
            $table->index("member_id");

            // 外部キー
            $table->foreign("member_id")->references("id")->on("members");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messages');
    }
}
