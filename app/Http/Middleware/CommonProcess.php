<?php

namespace App\Http\Middleware;

use Closure;
use App\Common\CommonMember;
use App\Common\CommonLike;
use App\Common\CommonDecline;
use App\Common\CommonFootprint;
use App\Common\CommonLog;
use App\Common\CommonTimeline;
use App\Common\CommonPricePlan;
class CommonProcess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            // 現在使用できる､有効な有料プランがDB内に在るかどうか
            $price_plans = CommonPricePlan::getPricePlan(Config("const.binary_type.on"));

            // 基本設定情報を取得する
            $basic = [
                "prefecture" => Config("const.prefecture"),
                "job_type" => Config("const.job_type"),
                "gender" => Config("const.gender"),
                "height" => Config("const.height"),
                // 検索時､身長要素ように設定
                "bottom_height" => Config("const.bottom_height"),
                "top_height" => Config("const.top_height"),
                "children" => Config("const.children"),
                "day_off" => Config("const.day_off"),
                "alcohol" => Config("const.alcohol"),
                "smoking" => Config("const.smoking"),
                "year" => Config("const.year"),
                "month" => Config("const.month"),
                "day" => Config("const.day"),
                "partner" => Config("const.partner"),
                "pet" => Config("const.pet"),
                "blood_type" => Config("const.blood_type"),
                "salary" => Config("const.salary"),
                "body_style" => Config("const.body_style"),
                "blur_level" => Config("const.image.blur_level"),
                "withdrawal" => Config("const.withdrawal"),
                "age_list" => Config("const.age_list"),
                // 検索時､年齢要素ように設定
                "bottom_ages" => Config("const.bottom_ages"),
                "top_ages" => Config("const.top_ages"),
                "price_plans" => $price_plans,
            ];

            $request->merge([
                "basic" => $basic,
                // html上のメタタグ
                "title" => Config("const.meta.title"),
                "description" => Config("const.meta.description"),
                "keywords" => Config("const.meta.keywords"),
                // アプリケーションからのメールアドレス
                "info_address" => Config("env.mail_from_address"),
                // ダミーアドレス
                "dummy_address" => Config("env.dummy_address"),
            ]);

            // エンドユーザー側ログインチェック
            // ログイン済みの場合、Requestオブジェクトにログイン中ユーザのデータを保持
            if($request->session()->has("member") === true) {
                $member = CommonMember::getSelfInfo($request->session()->get("member")->id);

                // なんからの理由でセッションからユーザー情報を取得できない場合は、ログアウトさせる
                if ($member === NULL) {
                    logger()->error("Sessionにログイン情報は持っていましたが､DB内に対象のレコードが見つかりませんでした｡");
                    $request->session()->forget("member");
                    return redirect()->action("Member\\IndexController@index");
                }
                $request->merge([
                    "member" => $member
                ]);
                // 除外ユーザー
                $excluded_users = CommonDecline::getExcludedUsers($request->member->id);
                $request->merge([
                    "excluded_users" => $excluded_users,
                ]);

                // 未読の足跡のID一覧を取得する(かならず配列を返却する)
                $uncheck_footprints = CommonFootprint::uncheckFootprints($request->member->id, $request->excluded_users);
                $request->merge([
                    "uncheck_footprints" => $uncheck_footprints,
                ]);

                // 未読のお知らせ一覧を取得する(かならず配列を返却する)
                $uncheck_logs = CommonLog::uncheckLogs($request->member->id, $request->excluded_users);
                $request->merge([
                    "uncheck_logs" => $uncheck_logs,
                ]);

                // 未読のタイムライン一覧を取得する(必ず配列を返却する)
                $uncheck_timelines = CommonTimeline::uncheckTimelines($request->member->id, $request->excluded_users);
                $request->merge([
                    "uncheck_timelines" => $uncheck_timelines,
                ]);


                // 送信済みのGoodの数
                $number_of_sending_likes = CommonLike::sendingLike($request->session()->get("member")->id, $request->excluded_users)->total();
                // 受け取ったGoodの数
                $number_of_getting_likes = CommonLike::gettingLike($request->session()->get("member")->id, $request->excluded_users)->total();
                // 有効なマッチング一覧
                $number_of_matching_users = CommonLike::getMatchingUsers($request->session()->get("member")->id, $request->excluded_users);
                if (isset($number_of_matching_users)) {
                    $number_of_matching_users = $number_of_matching_users->count();
                } else {
                    $number_of_matching_users = 0;
                }


                $request->merge([
                    // "sending_likes" => $sending_likes,
                    "number_of_sending_likes" => $number_of_sending_likes,
                    "number_of_getting_likes" => $number_of_getting_likes,
                    "number_of_matching_users" => $number_of_matching_users,
                ]);

                // 女性(本人確認)､男性(本人確認および有料プランの契約)を行った上でメッセージのやり取りができるかどうか
                // 利用必須条件
                if ($member->gender === "F") {
                    if (isset($member->identity_image) && (int)$member->identity_image->is_approved === Config("const.image.approve_type.authenticated")){
                        $is_identified = true;
                    } else {
                        $is_identified = false;
                    }
                    if ($is_identified) {
                        $request->merge([
                            "talkable" => true,
                            "is_identified" => $is_identified,
                        ]);
                    } else {
                        $request->merge([
                            "talkable" => true,
                            "is_identified" => $is_identified,
                        ]);
                    }
                } else {
                    $is_contracted = false;
                    $is_identified = false;
                    // 有料プランの契約状態
                    if (isset($member->valid_period) && (\strtotime($member->valid_period->format("Y-m-j H:i:s")) >= time())) {
                        $is_contracted = true;
                    }
                    // 本人確認状態
                    if (isset($member->identity_image) && (int)$member->identity_image->is_approved === Config("const.image.approve_type.authenticated")) {
                        $is_identified = true;
                    }
                    // ログインユーザーがメッセージのやり取りの条件を満たしているかどうか
                    if ($is_contracted && $is_identified) {
                        $request->merge([
                            "talkable" => true,
                            "is_contracted" => $is_contracted,
                            "is_identified" => $is_identified,
                        ]);
                    } else {
                        $request->merge([
                            "talkable" => true,
                            "is_contracted" => $is_contracted,
                            "is_identified" => $is_identified,
                        ]);
                    }
                }

                // プライオリティプランの検証
                if (isset($request->member->income_image) && (int)$request->member->income_image->is_approved === Config("const.image.approve_type.authenticated")) {
                    $request->merge([
                        "priority" => true,
                    ]);
                } else {
                    $request->merge([
                        "priority" => false,
                    ]);
                }
            }

            // 管理画面側ログインチェック
            if ($request->session()->has("administrator")) {
                // 管理画面側ログイン
                $request->merge([
                    "administrator" => $request->session()->get("administrator"),
                ]);
            }

            $response = $next($request);

            return $response->withHeaders([
                "Cache-Control" =>  "no-store, no-cache, must-revalidate, private, max-age=0",
                "Pragma" => "no-cache",
                "Expires" => "Thu, 01 Jan 1970 00:00:00 GMT",
                "Last-Modified" => gmdate( 'D, d M Y H:i:s' ).' GMT',
            ]);
        } catch (\Throwable $e) {
            // ミドルウェア内の例外発生時
            return response()->view("member.errors.index", [
                "request" => $request,
                "error" => $e,
            ], 400);
        }
    }
}
