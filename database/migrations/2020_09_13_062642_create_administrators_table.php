<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdministratorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('administrators', function (Blueprint $table) {
            $table->id();
            $table->string("email", 512)->unique();
            $table->string("password", 512);
            $table->string("display_name", 512);
            // ログイン可否状態を保持
            // 1 => ログイン可能, 2 => ログイン不可
            $table->tinyInteger("is_displayed")->default(0);
            $table->smallInteger("permission_level")->default(0);
            $table->text("memo")->nullable();
            // 最終ログイン日時
            $table->dateTime("last_login")->nullable();
            $table->dateTime("created_at")->nullable();
            $table->dateTime("updated_at")->nullable();
            // 論理削除
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
        Schema::dropIfExists('administrators');
    }
}
