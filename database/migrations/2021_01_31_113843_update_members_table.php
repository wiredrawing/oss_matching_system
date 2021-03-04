<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // マイグレーション実行時､ユニーク成約を外す
        Schema::table('members', function (Blueprint $table) {
            $table->dropUnique('members_display_name_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // ユニークキーをロールバック
        // マイグレーション実行時､ユニーク制約を付与しもとに戻す
        Schema::table('members', function (Blueprint $table) {
            $table->unique('display_name');
        });
    }
}
