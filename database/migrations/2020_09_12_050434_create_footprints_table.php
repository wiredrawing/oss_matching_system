<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFootprintsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('footprints', function (Blueprint $table) {
            $table->id();
            // 来訪元
            $table->bigInteger("from_member_id")->unsigned();
            // 来訪先
            $table->bigInteger("to_member_id")->unsigned();
            // 上記の組み合わせでの来訪回数
            $table->bigInteger("access_count");
            $table->tinyInteger("is_browsed")->default(0);
            $table->dateTime("created_at")->nullable();
            $table->dateTime("updated_at")->nullable();

            // 複合ユニークキー
            $table->unique(
                ["from_member_id", "to_member_id"],
                "unique_footprints_from_member_id_to_member_id"
            );

            // index
            $table->index(
                ["from_member_id", "to_member_id"],
                "index_footprints_from_member_id_to_member_id"
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
        Schema::dropIfExists('footprints');
    }
}
