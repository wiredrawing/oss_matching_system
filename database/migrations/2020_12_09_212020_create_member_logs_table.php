<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemberLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("member_id")->unsigned();
            $table->tinyInteger("login")->default(0)->nullable();
            $table->tinyInteger("logout")->default(0)->nullable();
            $table->text("http_user_agent")->nullable();

            // 外部キー
            $table->foreign("member_id")->references("id")->on("members");

            // index
            $table->index("member_id");

            $table->dateTime("created_at")->nullable();
            $table->dateTime("updated_at")->nullable();

            // 論理削除対応
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('member_logs');
    }
}
