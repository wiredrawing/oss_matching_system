<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WithdrawalLog;
use Illuminate\Http\Request;

class WithdrawController extends Controller
{



    /**
     * 現時点までに､退会した会員一覧および退会理由など
     *
     * @param Request $request
     * @return void
     */
    public function index(Request $request)
    {
        try {
            // 退会履歴を取得
            $withdrawal_logs = WithdrawalLog::with([
                "member" => function ($query) {
                    $query->withTrashed();
                }
            ])
            ->orderBy("id", "desc")
            ->paginate(Config("const.limit"));

            return view("admin.member.withdraw.index", [
                "request" => $request,
                "withdrawal_logs" => $withdrawal_logs,
            ]);
        } catch (\Throwable $e) {
            return view ("admin.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }
}
