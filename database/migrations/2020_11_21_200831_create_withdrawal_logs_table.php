<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWithdrawalLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('withdrawal_logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("member_id");
            // クレジットサーバー側の識別ID
            $table->string("credit_id", 256)->unique()->nullable();
            $table->tinyInteger("withdrawal");
            $table->text("opinion")->nullable();
            $table->dateTime("withdrawn_at");
            $table->dateTime("created_at")->nullable();
            $table->dateTime("updated_at")->nullable();

            $table->unique([
                "member_id",
                "credit_id",
            ],"withdrawal_logs_member_id_credit_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('withdrawal_logs');
    }
}
