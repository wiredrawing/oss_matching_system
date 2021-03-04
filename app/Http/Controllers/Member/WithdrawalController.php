<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\BaseWithdrawalRequest;
use App\Models\Member;
use App\Library\RandomToken;
use App\Common\CommonWithdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{



    /**
     * 退会処理確認画面
     *
     * @param BaseWithdrawal $request
     * @return void
     */
    public function index(BaseWithdrawalRequest $request)
    {
        return view("member.withdrawal.index", [
            "request" => $request,
        ]);
    }

    /**
     * 退会処理の実行
     *
     * @param BaseWithdrawal $request
     * @return void
     */
    public function postWithdrawal(BaseWithdrawalRequest $request)
    {
        try {
            // トランザクション開始
            DB::beginTransaction();
            $withdrawal_logs = CommonWithdrawal::withdraw($request);

            // NULLチェック(戻り値がNULL時は､退会処理失敗時)
            if ($withdrawal_logs === NULL) {
                throw new \Exception(Config("errors.FAILED_WITHDRAWING_ERR"));
            }

            logger()->info(__FILE__);
            logger()->info($withdrawal_logs);


            // 会員情報を論理削除する
            $member = Member::find($request->member->id);
            $delete_data = [
                "deleted_email" => $member->email,
                // emailカラムにはダミーのランダム文字を保持させる
                "email" => hash("sha512", RandomToken::MakeRandomToken(128, "WITHDRAW_")),
            ];
            $result = $member->fill($delete_data)->save();
            $result = $member->delete();

            // log
            logger()->info("会員情報を削除", ["member_id" => $request->member->id]);

            // 退会処理確定
            DB::commit();
            return redirect()->action("Member\\WithdrawalController@completed");
        } catch(\Throwable $e) {
            // 退会処理エラー時rollback();
            DB::rollback();
            return view("member.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }

    /**
     * 退会処理完了報告ページ
     *
     * @param BaseWithdrawalRequest $request
     * @return void
     */
    public function completed(BaseWithdrawalRequest $request)
    {
        return view("member.withdrawal.completed", [
            "request" => $request,
        ]);
    }
}
