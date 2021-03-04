<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimelinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('timelines', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("from_member_id")->unsigned();
            $table->bigInteger("to_member_id")->unsigned();
            // タイムライン上で、テキストメッセージ、URL、画像の3種からどのような扱いをするかどうか
            $table->tinyInteger("timeline_type");
            // 同テーブルには下記3カラムのいずれかが存在すること
            $table->integer("message_id")->nullable();
            $table->integer("url_id")->nullable();
            $table->integer("image_id")->nullable();
            $table->tinyInteger("is_browsed")->default(0);
            $table->dateTime("created_at")->nullable();
            $table->dateTime("updated_at")->nullable();

            // 複合index
            $table->index(
                ["from_member_id", "to_member_id"],
                "index_timelines_from_member_id_to_member_id"
            );
            // 外部キー
            $table->foreign("from_member_id")->references("id")->on("members");
            $table->foreign("to_member_id")->references("id")->on("members");
        });
    }

    /**
     * Reverse the migrations.
     *
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('timelines');
    }
}
