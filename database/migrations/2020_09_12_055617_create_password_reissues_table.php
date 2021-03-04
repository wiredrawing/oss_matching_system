<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePasswordReissuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('password_reissues', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("member_id")->unsigned();
            $table->string("token", 512);
            $table->dateTime("expired_at");
            $table->tinyInteger("is_used")->default(0);
            $table->dateTime("created_at")->nullable();
            $table->dateTime("updated_at")->nullable();

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
        Schema::dropIfExists('password_reissues');
    }
}
