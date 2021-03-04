<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateViolationReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // システム違反者通報用テーブル
        Schema::create('violation_reports', function (Blueprint $table) {
            $table->id();
            // 通報者
            $table->bigInteger("from_member_id")->unsigned();
            // 違反者
            $table->bigInteger("to_member_id")->unsigned();
            $table->text("message")->nullable();
            $table->dateTime("created_at")->nullable();
            $table->dateTime("updated_at")->nullable();

            // 論理削除を使用
            $table->softDeletes();

            // index
            $table->index([
                "from_member_id",
                "to_member_id"
            ],
            "index_violation_reports_from_member_id_to_member_id");

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
        Schema::dropIfExists('violation_reports');
    }
}
