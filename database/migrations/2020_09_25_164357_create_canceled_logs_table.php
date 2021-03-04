<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCanceledLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('canceled_logs', function (Blueprint $table) {
            $table->id();
            // 会員ID
            $table->bigInteger("member_id")->unsigned();
            // テレコムクレジット側で発行された決済ID
            $table->string("credit_id", 512);
            $table->dateTime("created_at")->nullable();
            $table->dateTime("updated_at")->nullable();

            // member_idとcredit_idは複合ユニークキーとする
            $table->unique([
                "member_id",
                "credit_id",
            ], "canceled_logs_member_id_credit_id");

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
        Schema::dropIfExists('canceled_logs');
    }
}
