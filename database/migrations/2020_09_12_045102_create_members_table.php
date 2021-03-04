<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->bigIncrements("id")->unsigned();
            $table->string("display_name", 512)->nullable()->unique();
            // $table->string("display_name_sort", 512)->nullable();
            $table->integer("age")->default(0);
            // $table->dateTime("birthday")->nullable();
            $table->string("gender", 1)->nullable(); // M:男性 F:女性
            $table->integer("height")->nullable();
            $table->tinyInteger("body_style")->nullable();
            $table->tinyInteger("children")->nullable();
            $table->tinyInteger("day_off")->nullable();
            $table->tinyInteger("alcohol")->nullable();
            $table->tinyInteger("smoking")->nullable();
            $table->string("message", 10240)->nullable();
            $table->tinyInteger("notification_good")->default(0);
            $table->tinyInteger("notification_message")->default(0);
            $table->tinyInteger("blood_type")->nullable();
            $table->tinyInteger("pet")->nullable();
            $table->tinyInteger("salary")->nullable();
            $table->tinyInteger("partner")->nullable();
            // 会員の契約プラン
            $table->string("plan_code", 16)->default("free")->nullable();
            // 会員の契約した有料プランの有効期間を保持
            $table->dateTime("valid_period")->nullable();
            $table->string("email", 512);
            // 退会時､メールアドレスはこのカラムに保存する
            $table->string("deleted_email", 512)->nullable();
            $table->string("password", 512)->nullable();
            $table->string("token", 512)->nullable();
            $table->string("security_token", 512)->nullable();
            $table->dateTime("expired_at")->nullable();
            $table->smallInteger("prefecture")->nullable();
            $table->smallInteger("job_type")->nullable();
            $table->tinyInteger("is_blacklisted")->default(0);
            $table->dateTime("last_login")->nullable();
            // 生IDで保存
            $table->string("credit_id", 64)->nullable();
            // 生パスワードで保存(本案件は基本　NAとなる)
            $table->string("credit_password", 64)->nullable();
            // 支払い開始日時
            $table->dateTime("start_payment_date")->nullable();
            // 本登録 or 仮登録
            $table->tinyInteger("is_registered")->default(0);
            // 管理者側用備考欄
            $table->text("memo")->nullable();
            // 本人確認 済み or 未済
            // 0:未認証 (初期値)
            // 1:申請中 (プロフィール画面で認証画像のアップロードが成功した場合)
            // 2:認証拒否 (管理画面側で認証画像を認証拒否した場合)
            // 3:認証済み (管理画面側で認証画像を承認した場合、以降変更不可)
            // 認証用画像をアップロードする度にステータスを申請中にする
            $table->smallInteger("is_approved")->default(0);
            // 本人確認した際の画像ID
            $table->bigInteger("approved_image_id")->nullable();

            // 収入証明書確認の 済み or 未済
            // 0:未申請 (条件 => 初期値) デフォルトプラン
            // 1:申請中 (条件 => プロフィール画面から収入証明をアップロードする)
            // 2:承認拒否 (条件 => 管理者側が管理画面から承認を拒否する)
            // 3:承認済み (条件 => 管理者側が管理画面から承認する)
            $table->smallInteger("income_certificate")->default(0);
            // 収入証明確認した際の画像ID
            $table->bigInteger("income_image_id")->nullable();
            $table->dateTime("created_at")->nullable();
            $table->dateTime("updated_at")->nullable();

            // 論理削除対応
            $table->softDeletes();

            // ユニークキーの定義
            $table->unique("email");
            $table->unique("token");
            // クレジットサーバー側識別子はユニークである前提
            $table->unique("credit_id");
            // セキュリティ用トークン
            $table->unique("security_token");

            // index
            $table->index("id");
            $table->index("email");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('members');
    }
}
