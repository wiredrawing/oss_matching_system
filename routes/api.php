<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


//////////////////
// Email関連
//////////////////
Route::group(["namespace" => "Api\\v1", "prefix" => "v1/email", "as" => "email."], function () {
    // メールアドレスによる仮登録
    Route::post("/", [
        "as" => "registerEmail",
        "uses" => "EmailController@register"
    ]);
    // 仮登録時のトークン検証
    Route::get("/{token}", [
        "as" => "checkToken",
        "uses" => "EmailController@checkToken",
    ]);
});

//////////////////
// Like関連
//////////////////
Route::group([
    "namespace" => "Api\\v1",
    "prefix" => "v1/like",
    "as" => "like.",
    "middleware" => "checkLoggedInStatus"], function () {
    // 任意の対象ユーザーに[Good]を送る
    Route::post("/", [
        "as" => "create",
        "uses" => "LikeController@create",
    ]);
    // 自身がおくったGood一覧を取得
    Route::get("/from/{member_id}", [
        "as" => "from",
        "uses" => "LikeController@getFrom",
    ]);
    // もらったGood一覧を取得
    Route::get("/to/{member_id}", [
        "as" => "to",
        "uses" => "LikeController@getTo"
    ]);
    // マッチングしているユーザー一覧を取得する
    Route::get("/matching/{member_id}", [
        "as" => "matching",
        "uses" => "LikeController@matching",
    ]);
});

//////////////////
// 画像アップロード関連
//////////////////
Route::group(["namespace" => "Api\\v1", "prefix" => "v1/media", "as" => "api.media.", "middleware" => "checkLoggedInStatus"], function () {
    // 画像のアップロード処理
    Route::post("/image", [
        "as" => "upload",
        "uses" => "MediaController@upload",
    ]);
    // 指定したログインユーザーのprofile_image一覧を取得する
    Route::get("/image/profile/{member_id}/{security_token}", [
        "as" => "profile.images",
        "uses" => "MediaController@getProfileImages"
    ]);
    // 指定した画像の表示処理
    Route::get("/image/{image_id}/{token}/{width?}", [
        "as" => "show",
        "uses" => "MediaController@show"
    ]);
    // 指定した画像の削除処理
    Route::post("/image/delete", [
        "as" => "delete",
        "uses" => "MediaController@delete",
    ]);
});

//////////////////
// Decline関連
//////////////////
Route::group(["namespace" => "Api\\v1", "prefix" => "v1/decline", "as" => "decline.", "middleware" => "checkLoggedInStatus"], function () {
    // 任意の対象ユーザーをブロックする
    Route::post("/", [
        "as" => "create",
        "uses" => "DeclineController@create",
    ]);
    // 自分自身がブロックしたユーザー一覧を取得
    Route::get("/{member_id}", [
        "as" => "get",
        "uses" => "DeclineController@get",
    ]);
    // 指定したユーザーIDのブロックを解除
    Route::delete("/", [
        "as" => "delete",
        "uses" => "DeclineController@delete",
    ]);
});

//////////////////
// TimeLineへのPOST系
//////////////////
Route::group(["namespace" => "Api\\v1", "prefix" => "v1/timeline", "as" => "api.timeline.", "middleware" => "checkLoggedInStatus"], function () {
    // api.timeline.message
    Route::post("/message", [
        "as" => "message",
        "uses" => "TimelineController@createMessage",
    ]);
    // api.timeline.message
    Route::post("/image", [
        "as" => "image",
        "uses" => "TimelineController@createImage",
    ]);
    // 指定したroomの投稿を最新順に取得する
    Route::get("/message/{from_member_id}/{to_member_id}/{offset}/{timeline_id}/{separator}", [
        "as" => "message",
        "uses" => "TimelineController@getMessage",
    ]);
});



//////////////////
// Member関連
//////////////////
Route::group(["namespace" => "Api\\v1", "prefix" => "v1/member", "as" => "member.", "middleware" => "checkLoggedInStatus"], function () {
    // ログイン認証処理を実行
    Route::post("/authenticate", [
        "as" => "authenticate",
        "uses" => "MemberController@authenticate",
    ]);
    // メールアドレス変更前の新規メールアドレス登録
    Route::put("/email/{member_id}", [
        "as" => "email",
        "uses" => "MemberController@email",
    ]);
    // 新規ユーザーの本登録処理
    Route::post("/{token}", [
        "as" => "create",
        "uses" => "MemberController@create"
    ]);
    // 指定したIDにマッチするユーザー情報を取得する
    Route::get("/{member_id}", [
        "as" => "show",
        "uses" => "MemberController@show"
    ]);
    // 指定したIDにマッチするユーザー情報の更新処理を実行する
    Route::put("/{member_id}", [
        "as" => "update",
        "uses" => "MemberController@update"
    ]);
    // ログアウト処理を実行
    Route::get("/logout", [
        "as" => "logout",
        "uses" => "MemberController@logout",
    ]);
});
