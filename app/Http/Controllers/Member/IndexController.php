<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\BaseEmailRequest;
use App\Http\Requests\BaseMemberRequest;
use App\Common\CommonMember;
use App\Common\CommonEmail;
use App\Common\CommonLike;
use App\Common\CommonFootprint;
// use App\Common\CommonDecline;
use App\Models\Image;
use App\Models\Member;
use App\Models\Decline;
use App\Library\Logger;
class IndexController extends Controller
{

    public function index(Request $request)
    {
        try {
            // 未ログインの場合、ログインページへリダイレクト
            if (isset($request->member) !== true) {
                return redirect()->action("Member\\LoginController@index");
            }
            // おすすめ一覧
            $recommended_users = CommonMember::getRecommendedUsers($request->member->id, $request->excluded_users);


            $member = CommonMember::getSelfInfo($request->member->id);
            // print_r($member->toArray());

            // 現在相互マッチングしているユーザー一覧
            $matching_users = CommonLike::getMatchingUsers($request->member->id, $request->excluded_users);

            // タイトルを設定
            $request->merge(["title" => $request->member->display_name."さんのマイページ"]);

            return view("member.index.index", [
                "request" => $request,
                "member" => $member,
                "recommended_users" => $recommended_users,
                "matching_users" => $matching_users,
            ]);
        } catch (\Exception $e) {
            Logger::error(__FILE__, $e);
            return view("member.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }

    /**
     * 現在ログイン中ユーザーのプロフィール編集画面
     *
     * @param BaseMemberRequest $request
     * @return void
     */
    public function edit(BaseMemberRequest $request)
    {
        try {
            $member = Member::where("id", $request->member->id)->with([
                "profile_images",
            ])->get()
            ->first();

            $profile_image_urls = [];
            foreach($member->profile_images as $key => $value) {
                $profile_image_urls[] = [
                    "image_id" => $value->id,
                    "url" => action("Api\\v1\\MediaController@show", [
                        "image_id" => $value->id,
                        "token" => $value->token,
                    ])
                ];
            }

            // 新規登録に必要なパラメータ
            return view("member.index.edit", [
                "request" => $request,
                "use_type" => Config("const.image.use_type.profile"),
                "is_approved" => Config("const.image.approve_type.none"),
                "blur_level" => 0,
                "profile_image_urls" => $profile_image_urls,
            ]);
        } catch(\Throwable $e) {
            Logger::error(__FILE__, $e);
            return view("member.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }

    /**
     * 現在ログイン中ユーザーのプロフィール更新処理
     *
     * @param BaseMemberRequest $request
     * @return void
     */
    public function postEdit(BaseMemberRequest $request)
    {
        try {

            $member = CommonMember::update($request, $request->member->id);

            // NULLチェック
            if ($member === NULL) {
                Logger::error(__FILE__, "ログインユーザーのプロフィール変更に失敗しました");
            }
            $request->merge([
                "member" => $member
            ]);
            return redirect()->action("Member\\IndexController@index");
        } catch(\Throwable $e) {
            Logger::error(__FILE__, $e);
            return view("member.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }



    /**
     * 本登録用入力フォーム(/member/create/{token})
     *
     * @param BaseEmailRequest $request
     * @param string $token
     * @return void
     */
    public function create (Request $request, string $token)
    {
        try {
            // 本登録用トークンの有効性チェック
            $member = CommonEmail::checkToken($token);
            // トークンの整合性をチェック
            logger()->info($member);
            if($member === NULL) {
                // 仮登録チェックで失敗した場合
                throw new \Exception(Config("errors.NOT_FOUND_ERR"));
            }

            // DB内にEmailアドレスが登録されているかどうか
            if (isset($member->email) !== true) {
                throw new \Exception (Config("errors.NOT_FOUND_ERR"));
            }

            // 仮登録のデータベースから取得したメールアドレス
            $email = $member->email;

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
            $body_style = Config("const.body_style");
            return view("member.index.create", [
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
                "token" => $token,
                "email" => $email,
                "request" => $request,
            ]);
        } catch (\Throwable $e) {
            return view("member.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }


    /**
     * 新規登録POST処理の実行
     *
     * @param BaseMemberRequest $request
     * @param string $token
     * @return void
     */
    public function postCreate(BaseMemberRequest $request, string $token)
    {
        try {
            // postデータの取得
            Logger::info(__FILE__, $request->validated());

            // 新規登録処理の実行
            $member = CommonMember::create($request, $token);

            if ($member === NULL) {
                // membersテーブルへの新規登録に失敗した場合
                Logger::error(__FILE__, "新規登録に失敗");
                throw new \Exception(Config("errors.CREATE_ERR"));
            }
            // 新規登録成功後、マイページTOPへリダイレクト
            return redirect()->action("Member\\IndexController@completed", [
                "token" => $member->token,
            ]);
        } catch (\Throwable $e) {
            Logger::error(__FILE__, $e);
            return view("member.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }

    /**
     * アカウントの本登録完了ページ
     *
     * @param BaseMemberRequest $request
     * @return void
     */
    public function completed(BaseMemberRequest $request)
    {
        return view("member.index.completed", [
            "request" => $request,
        ]);
    }

    /**
     * 本人認証用画像のアップロード画面
     *
     * @param BaseMemberRequest $request
     * @return void
     */
    public function identity(BaseMemberRequest $request)
    {
        try {
            return view("member.identity.index", [
                "request" => $request,
                // 画像用途は本人証明用
                "use_type" => Config("const.image.use_type.identity"),
                // 認証状態は認証承認中へ
                "is_approved" => Config("const.image.approve_type.applying"), // 認証申請中にする
                "blur_level" => 0,
            ]);
        } catch (\Throwable $e) {
            logger()->error($e);
            return view("member.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }

    /**
     * 本人証明書用画像アップロード完了ページ
     *
     * @param BaseMemberRequest $request
     * @return void
     */
    public function identityCompleted(BaseMemberRequest $request)
    {
        return view("member.identity.completed", [
            "request" => $request,
        ]);
    }


    /**
     * 本人認証用画像のアップロード画面
     *
     * @param BaseMemberRequest $request
     * @return void
     */
    public function income(BaseMemberRequest $request)
    {
        try {
            return view("member.income.index", [
                "request" => $request,
                // 画像用途は本人証明用
                "use_type" => Config("const.image.use_type.income"),
                // 認証状態は認証承認中へ
                "is_approved" => Config("const.image.approve_type.applying"), // 認証申請中にする
                "blur_level" => 0,
            ]);
        } catch (\Throwable $e) {
            logger()->error($e);
            return view("member.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }


    /**
     * 指定したユーザーの詳細情報を取得する
     *
     * @param BaseMemberRequest $request
     * @param integer $target_member_id
     * @return void
     */
    public function opponent(BaseMemberRequest $request, int $target_member_id)
    {
        try {
            // ログイン中ユーザーが指定の異性をブロックしていないかをチェック
            $decline = Decline::where([
                "from_member_id" => $request->member->id,
                "to_member_id" => $target_member_id,
            ])->get()
            ->first();
            if ($decline !== NULL) {
                logger()->info("現在、指定のユーザーをブロックしています。");
                throw new \Exception(Config("errors.BLOCK_USER_ERR"));
            }
            // ログイン中ユーザーが指定の異性にブロックされている場合
            $decline = Decline::where([
                "from_member_id" => $target_member_id,
                "to_member_id" => $request->member->id,
            ])->get()
            ->first();
            if ($decline !== NULL) {
                logger()->info("現在、指定の異性ユーザーからブロックされています。");
                logger()->info(__FILE__, $decline->toArray);
                throw new \Exception(Config("errors.NOT_FOUND_USER_ERR"));
            }

            // 足跡を残す
            $footprint = CommonFootprint::visit($request, $target_member_id);
            if ($footprint !== NULL) {
                logger()->info("足跡処理の実行", $footprint->toArray());
            }


            // ログインユーザーとターゲットユーザーのGood状態
            $is_match = CommonLike::isMatch($request->member->id, $target_member_id);
            $is_liking = CommonLike::isLiking($request->member->id, $target_member_id);
            $is_liked = CommonLike::isLiked($request->member->id, $target_member_id);

            // 現在のログイン中ユーザーが贈ったGood
            $likes = CommonLike::sendingLike($request->member->id);
            $likes = array_column($likes->toArray(), "to_member_id");

            // ターゲットが贈ったGood
            $target_likes = CommonLike::sendingLike($target_member_id);
            $target_likes = array_column($target_likes->toArray(), "to_member_id");

            // 閲覧対象のユーザー情報を取得
            $opponent = CommonMember::getMemberInfo($request->member->id, $target_member_id);

            // 対象ユーザーNULLチェック
            if ($opponent === NULL) {
                throw new \Exception(Config("errors.NOT_FOUND_USER_ERR"));
            }

            // deleted_atがNULLでない場合
            if ($opponent->deleted_at !== NULL) {
                // 削除から2年間が経過している場合は表示させない
                $today = (new \DateTime())->getTimestamp();
                if ( $today > ($opponent->deleted_at->timestamp + Config("const.loss_time")) ){
                    throw new \Exception (Config("errors.NOT_FOUND_ERR"));
                }
                // var_dump($today);
                // var_dump(Config("const.withdrawal.loss_time"));
                // var_dump(get_class($opponent->deleted_at));
                // var_dump($opponent->deleted_at->timestamp);
            }

            // 閲覧対象のプロフィール画像URL一覧を取得
            $profile_images = Image::where("member_id", $opponent->id)
            ->where("use_type", Config("const.image.use_type.profile"))
            ->get();
            $profile_urls = [];
            foreach ($profile_images as $key => $value) {
                $profile_urls [] = $value->image_url;
            }

            // log
            logger()->info("閲覧したいユーザー情報を取得");
            logger()->info($opponent);

            return view("member.index.opponent", [
                "partner" => Config("const.partner"),
                "pet" => Config("const.pet"),
                "height" => Config("const.height"),
                "body_style" => Config("const.body_style"),
                "job_type" => Config("const.job_type"),
                "alcohol" => Config("const.alcohol"),
                "smoking" => Config("const.smoking"),
                "day_off" => Config("const.day_off"),
                "prefecture" => Config("const.prefecture"),
                "children" => Config("const.children"),
                "salary" => Config("const.salary"),
                "blood_type" => Config("const.blood_type"),
                "request" => $request,
                "opponent" => $opponent,
                "profile_urls" => $profile_urls,
                "likes" => $likes,
                "target_likes" => $target_likes,
                "is_match" => $is_match,
                "is_liking" => $is_liking,
                "is_liked" => $is_liked,
            ]);
        } catch (\Throwable $e) {
            // var_dump($e->getLine());
            // log
            logger()->error($e);
            return view("member.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }

    /**
     * ログイン中ユーザーのメールアドレスを変更する
     *
     * @param BaseMemberRequest $request
     * @return void
     */
    public function email(BaseMemberRequest $request)
    {
        try {
            return view("member.index.email", [
                "request" => $request,
            ]);
        } catch (\Throwable $e) {
            // log
            logger()->error($e);
            return view("member.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }

    /**
     * メールアドレス変更処理の実行
     *
     * @param BaseMemberRequest $request
     * @return void
     */
    public function updateEmail(BaseMemberRequest $request)
    {
        try {
            $email_reset = CommonMember::email($request, $request->member->id);

            // nullチェック
            if ($email_reset === NULL) {
                throw new \Exception("メールアドレス変更手続きに失敗しました｡");
            }
            return view("member.index.updateEmail", [
                "request" => $request,
                "email_reset" => $email_reset,
            ]);
        } catch (\Throwable $e) {
            // log
            logger()->error($e);
            return view("member.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }

    /**
     * メールアドレス変更確定処理
     *
     * @param BaseMemberRequest $request
     * @return void
     */
    public function completedEmail(BaseMemberRequest $request, string $token)
    {
        try {
            //var_dump($request->validated());
            $result = CommonMember::completeUpdatingEmail($request, $token);

            // nullチェック
            if ($result === NULL) {
                throw new \Exception (Config("errors.FAILED_UPDATING_EMAIL_ERR"));
            }

            return view("member.index.completedEmail", [
                "request" => $request,
            ]);
        } catch (\Throwable $e) {
            // log
            logger()->error($e);
            return view("member.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }
}
