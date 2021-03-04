<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUrlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('urls', function (Blueprint $table) {
            $table->id();
            // member_idはint64型で保持
            $table->bigInteger("member_id")->unsigned();
            $table->string("url", 2048);
            $table->dateTime("created_at")->nullable();
            $table->dateTime("updated_at")->nullable();
            // 論理削除
            $table->softDeletes();

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
        Schema::dropIfExists('urls');
    }
}
