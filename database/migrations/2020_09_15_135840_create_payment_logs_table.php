<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("member_id")->unsigned();
            $table->string("credit_id", 512);
            $table->string("cont", 16)->nullable();
            $table->string("email", 512)->nullable();
            $table->bigInteger("money")->nullable()->default(0);
            // rebill_param_id
            $table->string("plan_code", 32)->nullable();
            $table->string("rel", 16)->nullable();
            $table->bigInteger("settle_count")->nullable()->default(0);
            $table->string("telno", 64)->nullable();
            $table->string("user_name", 512)->nullable();
            // 支払い日時(テレコムクレジットからリクエストが来た日にちとする)
            $table->timestamp("paid_at");
            // 解約日時
            $table->dateTime("canceled_at")->nullable();
            $table->dateTime("created_at")->nullable();
            $table->dateTime("updated_at")->nullable();
            // 一意性制約を設定
            $table->unique([
                "credit_id",
                "settle_count"
            ], "payment_logs_credit_id_settle_count");

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
        Schema::dropIfExists('payment_logs');
    }
}
