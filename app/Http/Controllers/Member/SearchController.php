<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Library\Logger;
use App\Models\Member;
use App\Common\CommonDecline;
use App\Http\Requests\BaseSearchRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class SearchController extends Controller
{



    /**
     * 異性の検索条件入力フォーム
     *
     * @param Request $request
     * @return void
     */
    public function index (Request $request)
    {
        try {
            // 新規登録に必要なパラメータ
            $prefecture = Config("const.prefecture");
            $job_type = Config("const.job_type");
            $gender = Config("const.gender");
            $height = Config("const.height");
            $children = Config("const.children");
            $day_off = Config("const.day_off");
            $alcohol = Config("const.alcohol");
            $smoking = Config("const.smoking");
            $year = Config("const.year");
            $month = Config("const.month");
            $day = Config("const.day");
            $partner = Config("const.partner");
            $pet = Config("const.pet");
            $blood_type = Config("const.blood_type");
            $salary = Config("const.salary");
            if ($request->member->gender === "M") {
                $body_style = Config("const.body_style")["F"];
            } else {
                $body_style = Config("const.body_style")["M"];
            }


            return view("member.search.index", [
                "request" => $request,
                "prefecture" => $prefecture,
                "job_type" => $job_type,
                "gender" => $gender,
                "height" => $height,
                "children" => $children,
                "day_off" => $day_off,
                "alcohol" => $alcohol,
                "smoking" => $smoking,
                "year" => $year,
                "month" => $month,
                "day" => $day,
                "partner" => $partner,
                "pet" => $pet,
                "blood_type" => $blood_type,
                "salary" => $salary,
                "body_style" => $body_style,
            ]);
        } catch (\Throwable $e) {
            Logger::error(__FILE__, $e);
            return view("member.errors.index", [
                "error" => $e,
            ]);
        }
    }



    /**
     * 異性の条件検索結果
     *
     * @param Request $request
     * @return void
     */
    public function list (BaseSearchRequest $request)
    {
        try {
            // print_r($request->validated());
            // 拒否しているあるいは拒否されているユーザーを除外
            $excluded_users = $request->excluded_users;

            // もらったGoodのサブクエリー
            $likes_sub_query = DB::table("likes")->select([
                "to_member_id",
                DB::raw("count(to_member_id) as total_likes")
            ])
            ->groupBy(["to_member_id"]);

            // プロフィール画像のサブクエリー
            $profile_images_sub_query = DB::table("images")->select([
                "member_id",
                DB::raw("max(images.id) as profile_image_id")
            ])
            ->where("images.use_type", Config("const.image.use_type.profile"))
            ->where("images.deleted_at", NULL)
            ->groupBy(["member_id"]);

            $profile_images_sub_query = DB::table("images")->select([
                "images.member_id",
                "images.updated_at",
                "profile_image_id",
                "token as profile_image_token"
            ])->joinSub($profile_images_sub_query, "profile_images", function($join) {
                $join->on("profile_images.profile_image_id", "=", "images.id");
            })->where("deleted_at", NULL);

            // 収入証明のサブクエリー
            $income_images_sub_query = DB::table("images")->select([
                "member_id",
                DB::raw("max(images.updated_at) as updated_at"),
                DB::raw("max(images.id) as income_image_id")
            ])
            ->where("images.use_type", Config("const.image.use_type.income"))
            ->where("is_approved", Config("const.image.approve_type.authenticated"))
            ->where("images.deleted_at", NULL)
            ->groupBy(["member_id"]);

            // $members = DB::table("members")->select([
            $members = Member::select([
                "members.id",
                "members.display_name",
                "members.prefecture",
                "members.updated_at",
                "members.age",
                "members.job_type",
                "members.message",
                "profile_images.profile_image_id",
                "profile_images.profile_image_token",
                // "income_images.income_image_id",
                DB::raw("case when income_images.income_image_id > 0 then income_images.income_image_id else 0 end as income_image_id"),
                DB::raw("case when total_likes > 0 then total_likes else 0 end as total_likes"),
            ])
            ->withTrashed()
            ->leftJoinSub($profile_images_sub_query, "profile_images", function ($join) {
                $join->on("profile_images.member_id", "=", "members.id");
            })
            ->leftJoinSub($income_images_sub_query, "income_images", function ($join) {
                $join->on("income_images.member_id", "=", "members.id");
            })
            ->leftJoinSub($likes_sub_query, "likes", function ($join) {
                $join->on("members.id", "=", "likes.to_member_id");
            })
            ->orderBy("income_image_id", "desc")
            ->orderBy("members.updated_at", "desc")
            ->orderBy("members.created_at", "desc")
            ->orderBy("members.id", "desc")
            ->orderBy("profile_images.updated_at", "desc");

            // 年齢下限
            if ($request->from_age > 0) {
                $members->where("age", ">=", $request->from_age);
            }
            // 年齢上限
            if ($request->to_age > 0) {
                $members->where("age", "<=", $request->to_age);
            }
            // 身長下限
            if ($request->bottom_height > 0) {
                $members->where("height", ">=", $request->bottom_height);
            }
            // 身長上限
            if ($request->top_height > 0) {
                $members->where("height", "<=", $request->top_height);
            }
            // お住まいのエリア
            if ($request->prefecture > 0) {
                $members->where("prefecture", $request->prefecture);
            }
            // 職業
            if ($request->job_type > 0) {
                $members->where("job_type", $request->job_type);
            }
            // 子供の有無
            if ($request->children > 0) {
                $members->where("children", $request->children);
            }
            // 休日
            if ($request->day_off > 0) {
                $members->where("day_off", $request->day_off);
            }
            // アルコール
            if ($request->alcohol > 0) {
                $members->where("alcohol", $request->alcohol);
            }
            // タバコ
            if ($request->smoking > 0) {
                $members->where("smoking", $request->smoking);
            }
            // パートナー
            if ($request->partner > 0) {
                $members->where("partner", $request->partner);
            }
            // ペット
            if ($request->pet > 0) {
                $members->where("pet", $request->pet);
            }
            // 血液型
            if ($request->blood_type > 0) {
                $members->where("blood_type", $request->blood_type);
            }
            // 年収
            if ($request->salary > 0) {
                $members->where("salary", $request->salary);
            }
            // // 任意のキーワード
            if (strlen($request->keyword) > 0) {
                $members->where(function ($query) use ($request) {
                    $query->where("message", "like", "%".$request->keyword."%")
                    // ->orWhere("email", "like", "%".mb_convert_kana($request->keyword, "c")."%")
                    // ->orWhere("email", "like", "%".mb_convert_kana($request->keyword, "C")."%")
                    ->orWhere("message", "like", "%".mb_convert_kana($request->keyword, "c")."%")
                    ->orWhere("message", "like", "%".mb_convert_kana($request->keyword, "C")."%")
                    ->orWhere("display_name", "like", "%".mb_convert_kana($request->keyword, "c")."%")
                    ->orWhere("display_name", "like", "%".mb_convert_kana($request->keyword, "C")."%")
                    ->orWhere("display_name", "like", "%".$request->keyword."%");
                });
            }

            // 特定のユーザーの除外を行う
            $members = $members->whereNotIn("members.gender", [$request->member->gender])
            ->whereNotIn("members.id", $excluded_users)
            ->where("members.is_registered", Config("const.binary_type.on"));
            // ->where("deleted_at", NULL);

            $members = $members->paginate(Config("const.limit"));
            $members->appends($request->validated());

            if ($members->total() === 0) {
                throw new \Exception("条件に合うユーザーはいませんでした｡");
            }

            return view("member.search.list", [
                "request" => $request,
                "members" => $members,
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
