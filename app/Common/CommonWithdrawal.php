<?php

namespace App\Common;

use App\Models\WithdrawalLog;
use App\Models\Member;
use App\Library\Logger;
use App\Http\Requests\BaseWithdrawalRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;

class CommonWithdrawal
{


    /**
     * 登録中ユーザーの退会処理を実行する
     * また､本アプリケーション退会処理内では､継続課金処理が生きている場合は失敗とする
     *
     * @param BaseWithdrawalRequest $request
     * @return \Illuminate\Database\Eloquent\Collection|null
     */
    public static function withdraw(BaseWithdrawalRequest $request) : ?\Illuminate\Database\Eloquent\Collection
    {
        try {
            // 退会ユーザーの詳細情報を取得する
            $member = Member::findOrFail($request->member_id);
            logger()->info("退会希望ユーザー", $member->toArray());

            // membersテーブルのcredit_idがある場合は､退会処理させない｡
            // 継続課金が生きているため､各ユーザーに自分で解約してもらう
            if (strlen($member->credit_id) > 0) {
                logger()->error("メンバーID: {$request->member_id}は､有料プラン継続中です｡");
                throw new \Exception(Config("errors.IN_CONTRACT_ERR"));
            }
            // // クレジットサーバーへ退会リクエストを送信する
            // $response = Http::asForm()->post(Config("const.telecom.withdrawal_url"), [
            //     "clientip" => Config("const.telecom.clientip"),
            //     "member_id" => $member->credit_id,
            //     // 本システムではテレコム側のパスワードは使用しない
            //     "password" => "NA",
            //     "mode" => "link",
            // ])->throw();

            // // クレジットサーバーからの退会処理レスポンス
            // Logger::info(__FILE__, $response);

            // // クレジットサーバーからの戻り値が[OK]であれば､退会処理成功
            // if ($response->body() !== "OK") {
            //     // 退会処理に失敗した場合､例外を投げる
            //     throw new \Exception(Config("errors.FAILED_WITHDRAWING_ERR"));
            // }

            // 退会処理実行日時
            $withdrawn_at = (new \DateTime())->format("Y-n-j H:i:s");
            // 退会理由をDBに保存
            foreach ($request->withdrawal as $key => $value) {
                $withdrawal_log = WithdrawalLog::create([
                    "member_id" => $request->member_id,
                    "credit_id" => $member->credit_id,
                    "opinion" => $request->opinion,
                    "withdrawal" => $request->withdrawal[$key],
                    "withdrawn_at" => $withdrawn_at,
                ]);
                Logger::info(__FILE__, $withdrawal_log);
            }

            // 本リクエストで退会した退会内容を返却する
            $withdrawal_logs = WithdrawalLog::where([
                "member_id" => $request->member_id,
                "credit_id" => $member->credit_id
            ])->get();

            // \Illuminate\Database\Eloquent\Collectionオブジェクトを返却
            return $withdrawal_logs;
        } catch(\Throwable $e) {
            logger()->error($e);
            throw new \Exception($e->getMessage());
            // return null;
        }
    }
}
