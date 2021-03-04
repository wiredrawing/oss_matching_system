<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateViolationCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('violation_categories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("violation_report_id")->unsigned();
            $table->integer("category_id");

            // 外部キーの指定
            $table->foreign("violation_report_id")->references("id")->on("violation_reports");
            $table->index("violation_report_id");

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
        Schema::dropIfExists('violation_categories');
    }
}
