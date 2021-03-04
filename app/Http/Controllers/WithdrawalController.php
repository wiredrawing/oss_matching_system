<?php

namespace App\Http\Controllers;

use App\Library\Logger;
use App\Models\CanceledLog;
use App\Models\Member;
use App\Common\CommonMember;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WithdrawalController extends Controller
{


    /**
     * クレジットサーバー側での継続課金のみを解除する処理とする
     *
     * @param Request $request
     * @return void
     */
    public function completed(Request $request)
    {
        try {
            logger()->info("実行中URL:".url()->full());
            // 有料プランの解約処理を実行
            $credit_id = (string)$request->member_id;
            $result = CommonMember::unsubscribe($credit_id);

            if ($result !== true) {
                throw new \Exception("有料プランの解約処理に失敗");
            }
            // クレジットサーバーにレスポンスの返却
            return response(Config("const.telecom.message"), 200)
            ->header("Content-Type", "text/plain")
            ->header("Pragma", "no-cache")
            ->header("Cache-Control", "no-cache, must-revalidate, max-age=0")
            ->header("Cache-Control", "post-check=0, pre-check=0")
            ->header("Powered-By", "Sandal-Works");
        } catch(\Exception $e) {
            logger()->error($e->getMessage());
            logger()->error("有料プランの解約処理失敗", [
                "credit_id" => $credit_id,
            ]);
            // クレジットサーバーにレスポンスの返却
            return response(Config("const.telecom.message"), 200)
            ->header("Content-Type", "text/plain")
            ->header("Pragma", "no-cache")
            ->header("Cache-Control", "no-cache, must-revalidate, max-age=0")
            ->header("Cache-Control", "post-check=0, pre-check=0")
            ->header("Powered-By", "Sandal-Works");
        }
    }
}
