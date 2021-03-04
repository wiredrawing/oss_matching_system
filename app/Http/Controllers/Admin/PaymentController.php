<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PaymentRequest;
use App\Models\CanceledLog;
use App\Models\PaymentLog;
use Illuminate\Http\Request;

class PaymentController extends Controller
{




    /**
     * 現時点で､完了済みの支払い済み履歴
     *
     * @param PaymentRequest $request
     * @return void
     */
    public function index(PaymentRequest $request)
    {
        try {
            $payment_logs = PaymentLog::with([
                "price_plan",
                "member" => function ($query) {
                    $query->withTrashed();
                }
            ])
            ->whereHas("member", function($query) use ($request) {
                if (strlen($request->keyword) > 0) {
                    $query->where("email", "like", "%{$request->keyword}%")
                    ->orWhere("display_name", "like", "%{$request->keyword}%")
                    ->orWhere("memo", "like", "%{$request->keyword}%")
                    ->orWhere("credit_id", "like", "%{$request->keyword}%")
                    ->orWhere("message", "like", "%{$request->keyword}%");
                }
            })
            ->orderBy("paid_at", "desc")
            ->paginate(Config("const.limit") * 2);
            return view("admin.member.payment.index", [
                "request" => $request,
                "payment_logs" => $payment_logs,
            ]);
        } catch (\Throwable $e) {
            logger()->error($e);
            return view("admin.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }

    /**
     * 現時点で､解約済み一覧
     *
     * @param PaymentRequest $request
     * @return void
     */
    public function canceled(PaymentRequest $request)
    {
        try {
            $canceled_logs = CanceledLog::with([
                "payment_log.price_plan",
                "member" => function ($query) {
                    $query->withTrashed();
                }
            ])
            ->orderBy("updated_at", "desc")
            ->paginate(Config("const.limit") * 2);

            // print_R($canceled_logs);
            return view("admin.member.payment.canceled", [
                "request" => $request,
                "canceled_logs" => $canceled_logs,
            ]);
        } catch (\Throwable $e) {
            logger()->error($e);
            return view("admin.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }
}
