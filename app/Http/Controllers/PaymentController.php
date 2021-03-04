<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Member;
use App\Models\PricePlan;
use App\Models\PaymentLog;

class PaymentController extends Controller
{





    /**
     * 決済サーバーより、決済完了の通知を受け取る
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function completed(Request $request, Response $response)
    {
        try {
            logger()->info("-------------[有料プランの決済処理開始]", $_SERVER);
            logger()->info("実行中URL:".url()->full());
            logger()->info($request->input());
            // 実行日時
            $today = (new \DateTime())->format("Y-n-j H:i:s");
            // クレジットサーバーから送信された､本アプリケーションのmember_id
            $member_id = (int)$request->input("option");
            // 初回都度決済か継続課金時か? yes(継続課金時) or no(初回都度課金時)
            $cont = $request->input("cont");
            // メールアドレス
            $email = $request->input("email");
            // クレジットサーバー側で判断する識別ID
            $credit_id = $request->input("sendid");
            // 課金金額
            $money = $request->input("money");
            // 契約した有料プラン
            $plan_code = $request->input("rebill_param_id");
            // 決済成功可否
            $rel = $request->input("rel");
            // 継続課金回数
            $settle_count = $request->input("settle_count");
            // 電話番号
            $telno = $request->input("telno");
            // クレジットカード名義
            $user_name = $request->input("user_name");
            $username = $request->input("username");

            if ( ($member_id > 0) !== true) {
                // member_idが取得できない場合は継続課金処理とみなす
                $member = Member::where("credit_id", $credit_id)->get()->first();
                if ($member === NULL) {
                    logger()->error("指定した､credit_idをもつ登録ユーザーが存在しません｡", ["credit_id" => $credit_id]);
                    throw new \Exception("初回決済処理が失敗しています｡");
                }
                $member_id = $member->id;
            }
            // 課金履歴を保持
            $payment_log = PaymentLog::create([
                "member_id" => $member_id,
                "credit_id" => $credit_id,
                "cont" => $cont,
                "email" => $email,
                "money" => $money,
                "plan_code" => $plan_code,
                "rel" => $rel,
                "settle_count" => $settle_count,
                "telno" => $telno,
                "user_name" => $user_name.$username,
                "paid_at" => $today,
            ]);

            // ここからトランザクション開始
            DB::beginTransaction();
            // 契約した有料プランの詳細情報をDBから取得
            $price_plan = PricePlan::where("plan_code", $plan_code)
            ->where("is_displayed", Config("const.binary_type.on"))
            ->get()
            ->first();

            // 現在有効な有料プランかどうかを検証する
            if ($price_plan === NULL) {
                logger()->error("plan_codeがprice_plansテーブルに存在しません｡");
                throw new \Exception("現在､購読不可能な有料プランです｡");
            }


            $d = new \DateTime();
            // 初回都度決済時の契約開始時
            $start_payment_date = $d->format("Y-m-j H:i:s");
            // 契約した有料プランの有効期間を取得
            $valid_period = $d->modify("{$price_plan->duration} day")->format("Y-m-j H:i:s");

            if ($member_id > 0 && $cont === "no") {
                // 初回都度決済時
                $member = Member::find($member_id);
                if ($member === NULL) {
                    logger()->error("指定した､member_idをもつ登録ユーザーが存在しません｡", ["member_id" => $member_id]);
                    throw new \Exception("初回決済処理が失敗しています｡");
                }
                // ユーザー情報の契約情報を更新する
                $result = $member->fill([
                    "credit_id" => $credit_id,
                    "plan_code" => $plan_code,
                    // 初回契約開始時
                    "start_payment_date" => $start_payment_date,
                    // 有料プランの有効期限
                    "valid_period" => $valid_period,
                ])->save();

                // 処理成功のlog
                logger()->info("初回都度決済の決済処理を実行完了", [
                    "member_id" => $member_id,
                    "credit_id" => $credit_id,
                    "valid_period" => $valid_period,
                    "start_payment_date" => $start_payment_date,
                    "plan_code" => $plan_code,
                    "result" => $result,
                ]);
            } else if ($cont === "yes") {
                // 上記以外は､継続決済時と判断する
                $member = Member::where("credit_id", $credit_id)->get()->first();
                if ($member === NULL) {
                    logger()->error("指定した､credit_idをもつ登録ユーザーが存在しません｡", ["credit_id" => $credit_id]);
                    throw new \Exception("継続決済処理が失敗しています｡");
                }
                // 有料プランの有効日時を現DB内カラムに加算する
                $d = \DateTime::createFromFormat("Y-m-j H:i:s", $member->valid_period->format("Y-m-j H:i:s"))->modify("{$price_plan->duration} day");
                $valid_period = $d->format("Y-m-j H:i:s");
                // ユーザーが見つかった場合､有料プランの継続処理を実行する
                $result = $member->fill([
                    "credit_id" => $credit_id,
                    "plan_code" => $plan_code,
                    // 有料プランの有効期限
                    "valid_period" => $valid_period,
                ])->save();

                // 処理成功のlog
                logger()->info("継続決済の継続処理を実行完了", [
                    "member_id" => $member_id,
                    "credit_id" => $credit_id,
                    "valid_period" => $valid_period,
                    "plan_code" => $plan_code,
                    "result" => $result,
                ]);
            } else {
                throw new \Exception("決済処理リクエストが届きましたが正常に処理できませんでした｡");
            }

            logger()->info("payment_logsテーブルに保存");
            logger()->info($payment_log);


            logger()->info("-------------[ここまで決済通知処理を完了]");
            DB::commit();

            // クレジットサーバーにレスポンスの返却
            return response(Config("const.telecom.message"), 200)
            ->header("Content-Type", "text/plain")
            ->header("Pragma", "no-cache")
            ->header("Cache-Control", "no-cache, must-revalidate, max-age=0")
            ->header("Cache-Control", "post-check=0, pre-check=0")
            ->header("Powered-By", "Sandal-Works");
        } catch (\Throwable $e){
            DB::rollback();
            logger()->error("決済処理完了通知処理に失敗しました｡");
            logger()->error($e);
            logger()->error("-------------[ここまで決済通知処理を完了!]");
            // クレジットサーバーにレスポンスの返却
            // 例外発生時は､[NG]という文字列を返す(これは､クレジットサーバーからのリクエスト再送をうながすため)
            return response(Config("const.telecom.ng"), 200)
            ->header("Content-Type", "text/plain")
            ->header("Pragma", "no-cache")
            ->header("Cache-Control", "no-cache, must-revalidate, max-age=0")
            ->header("Cache-Control", "post-check=0, pre-check=0")
            ->header("Powered-By", "Sandal-Works");
        }
    }
}
