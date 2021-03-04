<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeclinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // ブロックテーブルは物理削除する
        Schema::create('declines', function (Blueprint $table) {
            $table->id();
            // 拒否をした会員
            $table->bigInteger("from_member_id")->unsigned();
            // 拒否された会員
            $table->bigInteger("to_member_id")->unsigned();
            $table->dateTime("created_at")->nullable();
            $table->dateTime("updated_at")->nullable();
            // 複合ユニークキー
            $table->unique(
                [
                    "from_member_id",
                    "to_member_id"
                ],
                "declines_from_member_id_to_member_id_unique"
            );
            // 複合index
            $table->unique(
                [
                    "from_member_id",
                    "to_member_id"
                ],
                "declines_from_member_id_to_member_id_index"
            );

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
        Schema::dropIfExists('declines');
    }
}
