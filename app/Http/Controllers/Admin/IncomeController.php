<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\IncomeRequest;
use App\Models\Member;
use Illuminate\Http\Request;

class IncomeController extends Controller
{



    /**
     * 現在､本人確認申請中のユーザー一覧
     *
     * @param IdentityRequest $request
     * @return void
     */
    public function index(IncomeRequest $request)
    {
        try {
            $members = Member::withTrashed()
            ->with([
                "income_image",
            ])->where(function($query) use ($request) {
                // 検索キーワードが入力された場合のみ
                if (strlen($request->keyword) > 0) {
                    $query
                    ->where("email", "like", "%{$request->keyword}%")
                    ->orWhere("display_name", "like", "%{$request->keyword}%")
                    ->orWhere("message", "like", "%{$request->keyword}%")
                    ->orWhere("memo", "like", "%{$request->keyword}%");
                }
            })
            ->whereHas("income_image", function ($query) {
                $query->where("income_certificate", Config("const.image.approve_type.applying"));
            })
            // ->where("is_approved", Config("const.image.approve_type.applying"))
            ->paginate(Config("const.limit"));
            return view("admin.member.income.index", [
                "request" => $request,
                "members" => $members,
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
