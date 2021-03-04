<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePricePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('price_plans', function (Blueprint $table) {
            $table->id();
            // 利用可能なターゲットユーザー M or F
            $table->string("gender", 1);
            // クレジットサーバー側との連携につかうカラム
            $table->string("plan_code", 64)->unique();
            $table->string("template_code", 64);
            $table->tinyInteger("duration");
            $table->string("name", 32)->unique();
            $table->integer("price");
            // プラン選択可能フラグ
            $table->tinyInteger("is_displayed")->default(1);
            $table->dateTime("created_at")->nullable();
            $table->dateTime("updated_at")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('price_plans');
    }
}
