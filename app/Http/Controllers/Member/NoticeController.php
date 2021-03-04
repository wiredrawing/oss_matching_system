<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Common\CommonLog;
use App\Models\Log;
use App\Library\Logger;
use Illuminate\Http\Request;

class NoticeController extends Controller
{



    /**
     * マッチング履歴の表示
     *
     * @param Request $request
     * @return void
     */
    public function index(Request $request)
    {
        try {
            // 未読のお知らせを取得する
            $logs = CommonLog::getLogs($request->member->id, $request->excluded_users);

            if ($logs->count() === 0) {
                throw new \Exception("お知らせはありません｡");
            }

            // 未読フラグを既読にする
            $check_logs = CommonLog::checkLogs($request->member->id);
            return view("member.notice.index", [
                "request" => $request,
                "logs" => $logs,
            ]);
        } catch (\Throwable $e) {
            Logger::error(__FILE__, $e);
            return view("member.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }
}
