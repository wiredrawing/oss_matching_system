<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Like;
use App\Models\Image;
use App\Models\Footprint;
use App\Models\MemberLog;
use App\Models\PricePlan;
use App\Common\CommonLike;
use App\Http\Requests\Admin\MemberRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class MemberController extends Controller
{



    /**
     * 登録中会員一覧画面
     *
     * @param Request $request
     * @return void
     */
    public function index (MemberRequest $request)
    {
        try {
            $limit = Config("const.limit");
            $registering_status = Config("const.registering_status");
            if (strlen($request->email) > 0) {
                $members = Member::withTrashed()->where("email", "like", "%".$request->email."%")
                ->orWhere("deleted_email", "like", "%".$request->email."%")
                ->orWhere("id", "like", "%".$request->email."%")
                ->orWhere("display_name", "like", "%".mb_convert_kana($request->email, "c")."%")
                ->orWhere("display_name", "like", "%".mb_convert_kana($request->email, "C")."%")
                ->orWhere("memo", "like", "%".mb_convert_kana($request->email, "C")."%")
                ->orWhere("memo", "like", "%".mb_convert_kana($request->email, "C")."%")
                ->orWhere("message", "like", "%".mb_convert_kana($request->email, "C")."%")
                ->orWhere("message", "like", "%".mb_convert_kana($request->email, "C")."%")
                ->orderBy("id", "desc")
                ->paginate($limit);
            } else {
                $members = Member::withTrashed()->orderBy("id", "desc")
                ->paginate($limit);
            }

            $members->appends($request->validated());
            return view("admin.member.index", [
                "request" => $request,
                "members" => $members,
                "registering_status" => $registering_status,
            ]);
        } catch (\Throwable $e) {
            return view("admin.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }


    /**
     * 指定したユーザーの詳細表示画面
     *
     * @param MemberRequest $request
     * @param integer $member_id
     * @return void
     */
    public function detail(MemberRequest $request, int $member_id)
    {
        try {
            $member = Member::withTrashed()->with([
                "profile_images",
                "identity_image",
                "income_image",
            ])->findOrFail($member_id);

            // 現在設定中の課金プラン一覧を取得
            $price_plans = [];
            $price_plans_temp = PricePlan::select([
                "plan_code",
                "name"
            ])->get();

            foreach ($price_plans_temp as $key => $value) {
                $price_plans[$value->plan_code] = $value->name;
            }

            // 会員データ編集に必要なパラメータ
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
            $body_style = Config("const.body_style");
            $blacklist = Config("const.blacklist");
            $approve_type_name = Config("const.image.approve_type_name");
            $registering_status = Config("const.registering_status");
            $notification = Config("const.notification");
            $age_list = Config("const.age_list");

            return view("admin.member.detail", [
                "request" => $request,
                "member" => $member,
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
                "blacklist" => $blacklist,
                "approve_type_name" => $approve_type_name,
                "registering_status" => $registering_status,
                "notification" => $notification,
                "age_list" => $age_list,
                "price_plans" => $price_plans,
            ]);
        } catch (\Throwable $e) {
            return view("admin.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }


    /**
     *
     *
     * @param MemberRequest $request
     * @param integer $member_id
     * @return void
     */
    public function postDetail(MemberRequest $request, int $member_id)
    {
        try {
            DB::beginTransaction();
            // postデータの取得
            $post_data = $request->validated();
            // 会員情報の更新処理
            $member = Member::withTrashed()->findOrFail($member_id);

            // パスワードが入力されている場合に限り更新
            if (isset($post_data["password"])) {
                $post_data["password"] = password_hash($post_data["password"], PASSWORD_DEFAULT);
            }
            $result = $member->fill($post_data)->save();
            if ($result !== true) {
                throw new \Exception("会員情報のアップデートに失敗しました｡");
            }

            ////////////////////////////////////////////////////////
            // 編集対象の会員情報に､本人確認画像が申請中だった場合､画像も承認済みにする
            // 本人確認証明書の更新処理
            $identity_image = Image::where("member_id", $request->member_id)
            ->where("use_type", Config("const.image.use_type.identity"))
            ->get()
            ->first();
            if ($identity_image !== NULL) {
                $result = $identity_image->fill([
                    "is_approved" => $request->is_approved
                ])->save();
                if ($result !== true) {
                    throw new \Exception("会員情報のアップデートには成功しましたが､本人確認画像の更新に失敗しました｡");
                }
            }

            ////////////////////////////////////////////////////////
            // 編集対象の会員情報に､本人確認画像が申請中だった場合､画像も承認済みにする
            // 本人確認証明書の更新処理
            $income_image = Image::where("member_id", $request->member_id)
            ->where("use_type", Config("const.image.use_type.income"))
            ->get()
            ->first();
            if ($income_image !== NULL) {
                $result = $income_image->fill([
                    "is_approved" => $request->income_certificate
                ])->save();
                if ($result !== true) {
                    throw new \Exception("会員情報のアップデートには成功しましたが､収入証明書画像の更新に失敗しました｡");
                }
            }

            // 確定処理
            DB::commit();
            // 更新処理完了後､更新完了ページへリダイレクト
            return redirect()->action("Admin\\MemberController@completedDetail", [
                "member_id" => $member_id,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return view("admin.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }

    /**
     * ユーザー情報の更新処理が完了後の完了ページを表示
     *
     * @param MemberRequest $request
     * @param integer $member_id
     * @return void
     */
    public function completedDetail(MemberRequest $request, int $member_id)
    {
        $member = Member::findOrFail($request->member_id);
        return view("admin.member.completedDetail", [
            "request" => $request,
            "member" => $member,
        ]);
    }



    /**
     * 指定したユーザーが贈ったGood一覧を取得する
     *
     * @param MemberRequest $request
     * @param integer $member_id
     * @return void
     */
    public function sendingLike(MemberRequest $request, int $member_id)
    {
        try {
            $member = Member::withTrashed()->find($request->member_id);
            $likes = Like::with([
                "to_member" => function ($query) {
                    $query->withTrashed();
                }
            ])
            ->where("from_member_id", $request->member_id)
            ->get();

            return view("admin.member.like.send", [
                "request" => $request,
                "member" => $member,
                "likes" => $likes,
            ]);
        } catch (\Throwable $e){
            return view("admin.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }


    /**
     * 指定したユーザーが贈ったGood一覧を取得する
     *
     * @param MemberRequest $request
     * @param integer $member_id
     * @return void
     */
    public function gettingLike(MemberRequest $request, int $member_id)
    {
        try {
            $member = Member::withTrashed()->find($request->member_id);
            $likes = Like::with([
                "from_member" => function ($query) {
                    $query->withTrashed();
                }
            ])
            ->where("to_member_id", $request->member_id)
            ->get();

            return view("admin.member.like.get", [
                "request" => $request,
                "member" => $member,
                "likes" => $likes,
            ]);
        } catch (\Throwable $e) {
            return view("admin.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }


    /**
     * 指定したユーザーがこれまでアップロードした画像一覧を表示
     *
     * @param MemberRequest $request
     * @param integer $member_id
     * @return void
     */
    public function image(MemberRequest $request, int $member_id)
    {
        try {
            $member = Member::withTrashed()->find($request->member_id);
            $use_type_name = Config("const.image.use_type_name");
            $approve_type_name = Config("const.image.approve_type_name");

            $images = Image::withTrashed()
            ->where("member_id", $request->member_id)
            // ->whereNotIn("use_type", [
            //     Config("const.image.use_type.income"),
            //     Config("const.image.use_type.identity"),
            // ])
            ->orderBy("id", "desc")
            ->get();

            return view("admin.member.image", [
                "request" => $request,
                "member" => $member,
                "images" => $images,
                "use_type_name" => $use_type_name,
                "approve_type_name" => $approve_type_name,
            ]);
        } catch (\Throwable $e) {
            return view("admin.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }

    /**
     * 管理画面上で､指定した画像を論理削除する
     *
     * @param MemberRequest $request
     * @param integer $image_id
     * @return void
     */
    public function deleteImage(MemberRequest $request, int $member_id)
    {
        try {
            $image = Image::findOrFail($request->image_id)->delete();
            if ($image === true ) {
                return redirect()->action("Admin\\MemberController@image", [
                    "member_id" => $member_id,
                ]);
            }
            throw new \Exception("指定した画像の削除に失敗しました｡");
        } catch (\Throwable $e) {
            return view("admin.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }

    /**
     * 指定した会員についた足跡を取得
     *
     * @param MemberRequest $request
     * @param integer $member_id
     * @return void
     */
    public function footprint(MemberRequest $request, int $member_id)
    {
        try {
            $member = Member::withTrashed()->findOrFail($member_id);
            $footprints = Footprint::with([
                "from_member" => function ($query) {
                    $query->withTrashed();
                }
            ])
            ->whereHas("from_member")
            ->where("to_member_id", $request->member_id)
            ->get();

            return view("admin.member.footprint", [
                "request" => $request,
                "member" => $member,
                "footprints" => $footprints,
            ]);
        } catch (\Throwable $e) {
            return view("admin.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }

    /**
     * 指定したユーザーが､訪れたユーザー一覧
     *
     * @param MemberRequest $request
     * @param integer $member_id
     * @return void
     */
    public function visit(MemberRequest $request, int $member_id)
    {
        try {
            $member = Member::withTrashed()->findOrFail($request->member_id);
            $footprints = Footprint::with([
                "to_member" => function ($query) {
                    $query->withTrashed();
                }
            ])->where("from_member_id", $request->member_id)
            ->get();


            return view("admin.member.visit", [
                "request" => $request,
                "member" => $member,
                "footprints" => $footprints,
            ]);
        } catch (\Throwable $e) {
            return view("admin.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }

    /**
     * 指定した任意の会員ユーザーと相互マッチしているユーザー一覧を取得する
     *
     * @param MemberRequest $request
     * @param integer $member_id
     * @return void
     */
    public function matching(MemberRequest $request, int $member_id)
    {
        try {
            // 指定した閲覧中ユーザーとマッチングしているユーザー一覧を取得する
            $matching_users = CommonLike::getMatchingUsers($request->member_id);

            $member = Member::withTrashed()->findOrFail($request->member_id);
            return view("admin.member.like.match", [
                "request" => $request,
                "member" => $member,
                "matching_users" => $matching_users,
            ]);
        } catch (\Throwable $e) {
            return view("admin.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }

    /**
     * 指定した会員同士のメッセージのやり取りを表示
     *
     * @param MemberRequest $request
     * @param integer $member_id
     * @param integer $target_member_id
     * @return void
     */
    public function timeline(MemberRequest $request, int $member_id, int $target_member_id)
    {
        try {
            $member = Member::withTrashed()->findOrFail($member_id);
            $target_member = Member::withTrashed()->findOrFail($target_member_id);
            return view("admin.member.timeline", [
                "request" => $request,
                "member" => $member,
                "target_member" => $target_member,
            ]);
        } catch (\Throwable $e) {
            return view("admin.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }


    /**
     * 指定したユーザーのログイン履歴一覧を取得する
     *
     * @param MemberRequest $request
     * @param integer $number_id
     * @return void
     */
    public function history (MemberRequest $request, int $member_id)
    {
        try {
            // 閲覧中ユーザーのログイン履歴を取得
            $member = Member::with([
                "member_logs" => function ($query) {
                    $query->withTrashed();
                }
            ])
            ->withTrashed()
            ->findOrFail($member_id);
            //print_r($member->toArray());
            return view("admin.member.history", [
                "request" => $request,
                "member" => $member,
            ]);
        } catch (\Throwable $e) {
            return view("admin.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }
}
