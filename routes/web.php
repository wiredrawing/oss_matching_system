<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get("/", "Member\\IndexController@index");

Route::group(["as" => "batch."], function () {
    // batch.payment.completed
    Route::get("/payment/completed", [
        "as" => "payment.completed",
        "uses" => "PaymentController@completed",
    ]);
    // 継続課金退会通知
    // batch.withdrawal.completed
    Route::get("/withdrawal/completed", [
        "as" => "withdrawal.completed",
        "uses" => "WithdrawalController@completed",
    ]);
});

// 静的コンテンツ
Route::group(["as" => "static.",], function ()  {
    Route::get("/info", [
        "as" => "info",
        "uses" => "StaticPageController@info",
    ]);
    Route::get("/privacy", [
        "as" => "privacy",
        "uses" => "StaticPageController@privacy",
    ]);
    Route::get("/terms", [
        "as" => "terms",
        "uses" => "StaticPageController@terms",
    ]);
});
/////////////////////////////////////////////////////////////////////////
// 管理画面系
/////////////////////////////////////////////////////////////////////////
Route::group([
    "as" => "web.",
    "namespace" => "Admin",
    "prefix" => "admin"], function () {
    Route::group(["as" => "admin."], function () {

        // 管理画面TOP
        Route::get("/index", [
            "as" => "index",
            "uses" => "LoginController@login",
        ]);

        // 管理画面ログインフォーム
        Route::get("/login", [
            "as" => "login",
            "uses" => "LoginController@login",
        ]);
        // 管理画面ログイン処理
        Route::post("/authenticate", [
            "as" => "authenticate",
            "uses" => "LoginController@authenticate",
        ]);
        // 管理画面ログアウト処理
        Route::get("/logout", [
            "as" => "logout",
            "uses" => "LoginController@logout",
        ]);

        // 違反通報一覧
        Route::group(["as" => "violation.", "middleware" => "checkAdminLoggedInStatus"], function () {
            // 報告済み違反一覧
            Route::get("/violation", [
                "as" => "index",
                "uses" => "ViolationController@index"
            ]);
        });

        // 管理者アカウント管理
        Route::group(["as" => "staff.", "middleware" => "checkAdminLoggedInStatus"], function () {
            // スタッフ一覧
            Route::get("/staff/index", [
                "as" => "index",
                "uses" => "StaffController@index"
            ]);
            // 新規スタッフ作成
            Route::get("/staff/create", [
                "as" => "create",
                "uses" => "StaffController@create"
            ]);
            // 新規スタッフ作成処理
            Route::post("/staff/create", [
                "as" => "postCreate",
                "uses" => "StaffController@postCreate"
            ]);
            // 新規スタッフ作成処理の完了
            Route::get("/staff/create/completed", [
                "as" => "create.complete",
                "uses" => "StaffController@completed"
            ]);
            Route::get("/staff/update/{administrator_id}", [
                "as" => "update",
                "uses" => "StaffController@update"
            ]);
            Route::post("/staff/update/{administrator_id}", [
                "as" => "postUpdate",
                "uses" => "StaffController@postUpdate"
            ]);
            Route::get("/staff/create/completed", [
                "as" => "update.complete",
                "uses" => "StaffController@completed"
            ]);
        });

        // ユーザー情報関連
        Route::group(["as" => "member.", "middleware" => "checkAdminLoggedInStatus"], function () {
            // 会員一覧
            Route::get("/member", [
                "as" => "index",
                "uses" => "MemberController@index"
            ]);
            // 会員詳細画面
            Route::get("/member/{member_id}/detail", [
                "as" => "detail",
                "uses" => "MemberController@detail"
            ]);
            // 会員詳細更新処理
            Route::post("/member/{member_id}/detail", [
                "as" => "postDetail",
                "uses" => "MemberController@postDetail"
            ]);
            // 会員詳細情報更新完了後
            Route::get("/member/{member_id}/detail/completed", [
                "as" => "completedDetail",
                "uses" => "MemberController@completedDetail",
            ]);
            // 指定ユーザがもらったGood
            Route::get("/member/{member_id}/like/get", [
                "as" => "like.get",
                "uses" => "MemberController@gettingLike"
            ]);
            // 指定ユーザーが贈ったGood
            Route::get("/member/{member_id}/like/send", [
                "as" => "like.send",
                "uses" => "MemberController@sendingLike"
            ]);
            // 指定ユーザーのマッチング一覧
            Route::get("/member/{member_id}/like/match", [
                "as" => "like.match",
                "uses" => "MemberController@matching"
            ]);
            // 指定ユーザーにつけられた足跡一覧
            Route::get("/member/{member_id}/footprint", [
                "as" => "footprint",
                "uses" => "MemberController@footprint"
            ]);
            // 指定ユーザーが訪れたユーザー一覧
            Route::get("/member/{member_id}/visit", [
                "as" => "footprint",
                "uses" => "MemberController@visit"
            ]);
            // 指定ユーザーがアップした画像一覧
            Route::get("/member/{member_id}/image", [
                "as" => "image",
                "uses" => "MemberController@image"
            ]);
            // 指定したユーザーがアップした画像を論理削除する
            Route::post("/member/{member_id}/image/delete", [
                "as" => "image.delete",
                "uses" => "MemberController@deleteImage"
            ]);
            // 指定ユーザーのタイムラインやり取り
            Route::get("/member/{member_id}/timeline/{target_member_id}", [
                "as" => "timeline",
                "uses" => "MemberController@timeline"
            ]);

            // 指定したユーザーの過去のログイン履歴一覧を取得する
            Route::get("/member/{member_id}/history", [
                "as" => "history",
                "uses" => "MemberController@history"
            ]);

            // 身分証申請一覧
            Route::group(["as" => "identity."] , function () {
                Route::get("/member/identity", [
                    "as" => "index",
                    "uses" => "IdentityController@index"
                ]);
                Route::get("/member/identity/{member_id}/detail", [
                    "as" => "detail",
                    "uses" => "IdentityController@detail",
                ]);
            });

            // 収入証明申請一覧
            Route::group(["as" => "income."] , function () {
                Route::get("/member/income", [
                    "as" => "index",
                    "uses" => "IncomeController@index"
                ]);
                Route::get("/member/identity/{member_id}/detail", [
                    "as" => "detail",
                    "uses" => "IdentityController@detail",
                ]);
            });

            // クレジット決済済み一覧
            Route::group(["as" => "payment."], function () {
                Route::get("/member/payment/index", [
                    "as" => "index",
                    "uses" => "PaymentController@index",
                ]);
                // 解約済み一覧
                Route::get("/member/payment/canceled", [
                    "as" => "canceled",
                    "uses" => "PaymentController@canceled",
                ]);
            });

            // 退会履歴一覧
            Route::group(["as" => "withdraw."], function () {
                Route::get("/member/withdraw/index", [
                    "as" => "index",
                    "uses" => "WithdrawController@index",
                ]);
            });
        });
    });
});


// 非ログインルート
// パスワード再発行処理
Route::group(["as" => "web.password.", "middleware" => "checkNoneLoggedInStatus"], function () {
    Route::get("/password/update/completed", [
        "as" => "completed",
        "uses" => "PasswordController@completed",
    ]);
    Route::get("/password/update/{token}", [
        "as" => "update",
        "uses" => "PasswordController@update",
    ]);
    Route::post("/password/update/{token}", [
        "as" => "postUpdate",
        "uses" => "PasswordController@postUpdate",
    ]);
});

// 非ログインルート
Route::group(["as" => "web.member.email.", "middleware" => "checkNoneLoggedInStatus"], function () {
    // web.member.email.reissue
    Route::get("/email/reissue", [
        "as" => "reissue",
        "uses" => "Member\\EmailController@reissue"
    ]);
    // web.member.email.reissue
    Route::post("/email/reissue", [
        "as" => "reissue",
        "uses" => "Member\\EmailController@postReissue"
    ]);
    // ■メールアドレスによる仮登録
    // web.member.email.completed
    Route::get("/email/completed", [
        "as" => "completed",
        "uses" => "Member\\EmailController@completed",
    ]);

    // web.member.email.register
    Route::post("/email/register", [
        "as" => "register",
        "uses" => "Member\\EmailController@register",
    ]);

    // web.member.email.index
    Route::get("/email/index", [
        "as" => "index",
        "uses" => "Member\\EmailController@index"
    ]);

    // web.member.email.index
    Route::get("/email", [
        "as" => "index",
        "uses" => "Member\\EmailController@index"
    ]);
});



// 非ログインルート
Route::group(["as" => "web.member.", "middleware" => "checkNoneLoggedInStatus"], function() {
    // web.member.create
    Route::get("/member/create/{token}", [
        "as" => "create",
        "uses" => "Member\\IndexController@create"
    ]);

    // web.member.create
    Route::post("/member/create/{token}", [
        "as" => "create",
        "uses" => "Member\\IndexController@postCreate"
    ]);
    // web.member.create.completed
    Route::get("/member/create/{token}/completed", [
        "as" => "create.completed",
        "uses" => "Member\\IndexController@completed"
    ]);

    // ■ログインフォーム
    // web.member.login
    Route::get("/member/login", [
        "as" => "login",
        "uses" => "Member\\LoginController@index"
    ]);

    // web.member.authenticate
    Route::post("/member/authenticate", [
        "as" => "authenticate",
        "uses" => "Member\\LoginController@authenticate"
    ]);
});


// 要ログインルート
Route::group(["as" => "web.member.", "middleware" => "checkLoggedInStatus"], function () {

    // ■ログイン後ユーザーマイページ
    Route::get("/member", [
        "as" => "index",
        "uses" => "Member\\IndexController@index"
    ]);

    // ログインユーザー編集画面
    Route::get("/member/edit",[
        "as" => "edit",
        "uses" => "Member\\IndexController@edit",
    ]);
    // ログインユーザープロフィール編集処理実行
    Route::post("/member/edit",[
        "as" => "edit",
        "uses" => "Member\\IndexController@postEdit",
    ]);

    // ■指定したmember_idの情報を取得
    Route::get("/member/{target_member_id}/opponent", [
        "as" => "opponent",
        "uses" => "Member\\IndexController@opponent"
    ]);

    // 異性のユーザーをブロックする処理
    Route::group(["as" => "decline."], function() {
        // ■現在ブロックしているユーザー一覧を取得
        Route::get("/member/decline", [
            "as" => "index",
            "uses" => "Member\\DeclineController@index",
        ]);

        // 指定したユーザーをブロックする
        Route::post("/member/decline", [
            "as" => "block",
            "uses" => "Member\\DeclineController@block",
        ]);

        // 指定したユーザーのブロック完了ページ
        Route::get("/member/decline/completed", [
            "as" => "completed",
            "uses" => "Member\\DeclineController@completedBlocking",
        ]);

        // 指定したユーザーをブロックから解除する
        Route::post("/member/decline/unblock", [
            "as" => "unblock",
            "uses" => "Member\\DeclineController@unblock",
        ]);
    });

    // 異性へのGoodを処理する
    Route::group(["as" => "like."], function () {
        // 相互マッチ中ユーザー一覧
        Route::get("/member/like/match", [
            "as" => "get",
            "uses" => "Member\\LikeController@matching"
        ]);

        // Goodをくれたユーザー一覧
        Route::get("/member/like/get", [
            "as" => "get",
            "uses" => "Member\\LikeController@getLike"
        ]);

        // 贈ったGood一覧
        Route::get("/member/like/send", [
            "as" => "send",
            "uses" => "Member\\LikeController@sendLike"
        ]);

        // Goodをおくる処理
        Route::post("/member/like", [
            "as" => "like.create",
            "uses" => "Member\\LikeController@create"
        ]);
    });


    // 足跡確認画面
    Route::get("/member/footprint", [
        "as" => "footprint",
        "uses" => "Member\\FootprintController@index"
    ]);


    // ■ログアウトフォーム
    Route::get("/member/logout", [
        "as" => "logout",
        "uses" => "Member\\LoginController@logout"
    ]);

    // ■本人確認証明書アップロードフォーム
    Route::get("/member/identity", [
        "as" => "identity",
        "uses" => "Member\\IndexController@identity"
    ]);

    // ■本人確認証明書アップロード完了画面
    Route::get("/member/identity/completed", [
        "as" => "identity",
        "uses" => "Member\\IndexController@identityCompleted"
    ]);

    // ■収入証明アップロードフォーム
    Route::get("/member/income", [
        "as" => "income",
        "uses" => "Member\\IndexController@income"
    ]);

    // ■収入証明アップロード完了画面
    Route::get("/member/income/completed", [
        "as" => "income",
        "uses" => "Member\\IndexController@identityCompleted"
    ]);

    // 異性の検索条件入力フォーム
    Route::get("/member/search", [
        "as" => "search",
        "uses" => "Member\\SearchController@index"
    ]);
    // 異性の検索条件結果一覧画面
    Route::get("/member/search/list", [
        "as" => "search.list",
        "uses" => "Member\\SearchController@list"
    ]);

    // 指定したマッチ済みユーザーとのトーク関連
    Route::group(["as" => "message."], function () {
        Route::get("/member/message", [
            "as" => "index",
            "uses" => "Member\\MessageController@index"
        ]);
        Route::get("/member/message/{to_member_id}/talk", [
            "as" => "talk",
            "uses" => "Member\\MessageController@talk"
        ]);
    });

    // マッチング履歴画面(お知らせ)
    Route::group(["as" => "notice."], function () {
        Route::get("/member/notice", [
            "as" => "index",
            "uses" => "Member\\NoticeController@index"
        ]);
    });

    // 設定画面
    Route::group(["as" => "config."], function () {
        Route::get("/member/config", [
            "as" => "index",
            "uses" => "Member\\ConfigController@index"
        ]);
    });

    // ログイン中におけるパスワード変更処理
    Route::group(["as" => "password."], function () {
        Route::get("/member/password", [
            "as" => "index",
            "uses" => "Member\\PasswordController@index",
        ]);
        Route::post("/member/password/postUpdate", [
            "as" => "postUpdate",
            "uses" => "Member\\PasswordController@postUpdate",
        ]);
        Route::get("/member/password/completed", [
            "as" => "completed",
            "uses" => "Member\\PasswordController@completed",
        ]);
    });

    // 会員側ページ退会処理
    Route::group(["as" => "withdrawal."], function () {
        Route::get("/member/withdrawal", [
            "as" => "index",
            "uses" => "Member\\WithdrawalController@index",
        ]);
        Route::post("/member/withdrawal", [
            "as" => "postWithdrawal",
            "uses" => "Member\\WithdrawalController@postWithdrawal",
        ]);
        Route::get("/member/withdrawal/completed", [
            "as" => "completed",
            "uses" => "Member\\WithdrawalController@completed",
        ]);
    });

    // 有料プラン機能
    Route::group(["as" => "subscribe."], function() {
        // 新規､有料プラン選択画面
        Route::get("/member/subscribe", [
            "as" => "index",
            "uses" => "Member\\SubscribeController@index"
        ]);
        // 契約中の有料プランの解約処理
        Route::post("/member/subscribe/unsubscribe", [
            "as" => "unsubscribe",
            "uses" => "Member\\SubscribeController@unsubscribe",
        ]);
        // 有料プラン契約完了画面
        Route::get("/member/subscribe/completedSubscribing", [
            "as" => "completed",
            "uses" => "Member\\SubscribeController@completedSubscribing"
        ]);
        // 契約済み有料プラン契約解約完了画面
        Route::get("/member/subscribe/completedUnsubscribing", [
            "as" => "completed",
            "uses" => "Member\\SubscribeController@completedUnsubscribing"
        ]);
    });

    // 違反者通報
    Route::group(["as" => "violation."], function () {
        Route::get("/member/violation/create/{member_id}", [
            "as" => "create",
            "uses" => "Member\\ViolationController@create",
        ]);
        Route::post("/member/violation/create/{member_id}", [
            "as" => "postCreate",
            "uses" => "Member\\ViolationController@postCreate",
        ]);
        Route::get("/member/violation/completed/{member_id}", [
            "as" => "completed",
            "uses" => "Member\\ViolationController@completed",
        ]);
    });

    // ログイン中ユーザーのメールアドレス変更処理
    Route::group(["as" => "email."], function () {
        Route::get("/member/email/change", [
            "as" => "index",
            "uses" => "Member\\IndexController@email",
        ]);
        Route::post("/member/email/update", [
            "as" => "update",
            "uses" => "Member\\IndexController@updateEmail"
        ]);
        Route::get("/member/email/completed/{token}", [
            "as" => "completed",
            "uses" => "Member\\IndexController@completedEmail"
        ]);
    });
});
