<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            // 保有する会員ID
            $table->bigInteger("member_id")->unsigned();
            // 画像の用途 (用途には以下の4種類を用いる)
            // 0:仕様上はありえない
            // 1:タイムライン用途
            // 2:本人確認用画像
            // 3:プロフィール画像用途(3枚アップ可能)
            // 4:収入証明用画像
            $table->tinyInteger("use_type");
            // アップロード後、対象のディレクトリに保存された際の画像ファイル名
            $table->string("filename", 512);
            // 画像のぼかしレベル  (0% ～ 100%)
            // デフォルトでは0%(ぼかしなし)
            $table->tinyInteger("blur_level")->default(0);
            // 画像用途が、認証が必要な場合
            $table->tinyInteger("is_approved")->nullable();
            $table->dateTime("created_at")->nullable();
            $table->dateTime("updated_at")->nullable();

            // $table->unique("filename");

            // URLリクエスト時に必要な参照トークン
            $table->string("token", 512);
            // 論理削除
            $table->softDeletes();

            // unique
            $table->unique("token");

            // index
            $table->index("member_id");
            $table->index("filename");

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
        Schema::dropIfExists('images');
    }
}
