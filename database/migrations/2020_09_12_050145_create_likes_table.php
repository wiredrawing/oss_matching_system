<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Goodテーブルは物理削除する
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            // Goodした人
            $table->bigInteger("from_member_id")->unsigned();
            // Goodされた人
            $table->bigInteger("to_member_id")->unsigned();
            // お気に入りフラグ
            $table->tinyInteger("favorite")->default(0);
            $table->dateTime("created_at")->nullable();
            $table->dateTime("updated_at")->nullable();

            // ユニークキー
            $table->unique([
                "from_member_id",
                "to_member_id"
            ],
            "unique_likes_from_member_id_to_member_id");

            // index
            $table->index([
                "from_member_id",
                "to_member_id"
            ],
            "index_likes_from_member_id_to_member_id");

            // 外部キー
            $table->foreign("from_member_id")->references("id")->on("members");
            $table->foreign("to_member_id")->references("id")->on("members");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('likes');

    }
}
