<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("from_member_id")->unsigned();
            $table->bigInteger("to_member_id")->unsigned();
            $table->tinyInteger("action_id");
            $table->tinyInteger("is_browsed")->default(0);
            $table->dateTime("created_at")->nullable();
            $table->dateTime("updated_at")->nullable();

            // 複合index
            $table->index(
                ["from_member_id", "to_member_id"],
                "index_logs_from_member_id_to_member_id"
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
        Schema::dropIfExists('logs');
    }
}
