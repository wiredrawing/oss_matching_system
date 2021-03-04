<?php

namespace App\Http\Middleware;

use Closure;

use Illuminate\Support\Facades\DB;
class CheckAdminLoggedInStatus
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
        if (isset($request->administrator) && $request->administrator !== NULL) {
            // 本人確認申請中会員を取得
            $identity_sub_query = DB::table("images")->select([
                "member_id",
                "is_approved",
                DB::raw("max(id) as id"),
            ])
            ->where("use_type", Config("const.image.use_type.identity"))
            ->where("is_approved", Config("const.image.approve_type.applying"))
            ->where("deleted_at", NULL)
            ->groupBy([
                "member_id",
                "is_approved",
            ]);
            $users_applying_identity = DB::table("members")
            ->select([
                "members.id as member_id",
                "identity_images.id as image_id",
                "identity_images.is_approved",
            ])->joinSub($identity_sub_query, "identity_images", function ($join) {
                $join->on("members.id", "=", "identity_images.member_id");
            })
            ->where("members.deleted_at", NULL)
            ->get();

            // print_r($users_applying_identity->toArray());

            $request->merge([
                "users_applying_identity" => $users_applying_identity,
            ]);

            // 収入証明申請中会員を取得
            $income_sub_query = DB::table("images")->select([
                "member_id",
                "is_approved",
                DB::raw("max(id) as id")
            ])
            ->where("use_type", Config("const.image.use_type.income"))
            ->where("is_approved", Config("const.image.approve_type.applying"))
            ->where("deleted_at", NULL)
            ->groupBy([
                "member_id",
                "is_approved",
            ]);
            // 収入証明申請中会員取得のクエリ
            $users_applying_income = DB::table("members")
            ->select([
                "members.id as member_id",
                "income_images.id as image_id",
                "income_images.is_approved",
            ])
            ->joinSub($income_sub_query, "income_images", function ($join) {
                $join->on("members.id", "=", "income_images.member_id");
            })
            ->where("members.deleted_at", NULL)
            ->get();

            // print_r($users_applying_income->toArray());

            $request->merge([
                "users_applying_income" => $users_applying_income,
            ]);

            return $next($request);
        }
        return redirect()->action("Admin\\LoginController@login");
    }
}
