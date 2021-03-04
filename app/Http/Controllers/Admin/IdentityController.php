<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\IdentityRequest;
use App\Models\Member;
use Illuminate\Http\Request;

class IdentityController extends Controller
{



    /**
     * 現在､本人確認申請中のユーザー一覧
     *
     * @param IdentityRequest $request
     * @return void
     */
    public function index(IdentityRequest $request)
    {
        try {
            $members = Member::withTrashed()
            ->with([
                "identity_image",
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
            ->whereHas("identity_image", function ($query) {
                $query->where("is_approved", Config("const.image.approve_type.applying"));
            })
            // ->where("is_approved", Config("const.image.approve_type.applying"))
            ->paginate(Config("const.limit"));
            return view("admin.member.identity.index", [
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
