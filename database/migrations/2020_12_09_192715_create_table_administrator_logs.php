<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableAdministratorLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('administrator_logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("administrator_id")->unsigned();
            $table->tinyInteger("login")->default(0)->nullable();
            $table->tinyInteger("logout")->default(0)->nullable();
            $table->longText("http_user_agent")->nullable();

            // 外部キー
            $table->foreign("administrator_id")->references("id")->on("administrators");
            // インデックス
            $table->index("administrator_id");

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
        Schema::dropIfExists('administrator_logs');
    }
}
