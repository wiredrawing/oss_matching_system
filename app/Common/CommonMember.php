<?php

namespace App\Common;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\BaseMemberRequest;
use App\Models\Member;
use App\Models\Image;
use App\Models\EmailReset;
use App\Models\Decline;
use App\Models\CanceledLog;
use App\Library\RandomToken;
use App\Models\MemberLog;

class CommonMember
{

    /**
     * 仮登録レコードをベースに、新規ユーザー作成
     *
     * @param BaseMemberRequest $request
     * @param string $token
     * @return array
     */
    public static function create(BaseMemberRequest $request, string $token): ?\App\Models\Member
    {
        try {
            $post_data = $request->validated();
            // 下記新規登録の条件を満たす場合のみ、登録処理を実施する
            // 指定された、tokenカラムが一致且つ、有効期限内、且つメールアドレスがマッチ
            $member = Member::where("token", $post_data["token"])
            ->where("email", $post_data["email"])
            ->where("expired_at", ">=", (new \DateTime())->format("Y-n-j H:i:s"))
            ->where("is_registered", config("const.binary_type.off"))
            ->get()
            ->first();

            // 仮登録ユーザーがみつかない場合は、例外を投げる
            if ($member === NULL) {
                // log
                logger()->error(__FILE__, $post_data);
                logger()->error("仮登録ユーザーが見つかりませんでした。");

                throw new \Exception(Config("errors.NOT_FOUND_ERR"));
            }

            // 仮登録ユーザーの本登録処理を実行
            $post_data["password"] = password_hash($post_data["password"], PASSWORD_DEFAULT);
            $post_data["is_registered"] = Config("const.binary_type.on");
            $res = $member->fill($post_data)->save();

            // 更新処理に失敗した場合
            if ($res !== true) {
                // log
                logger()->error(__FILE__);
                logger()->error("ユーザー情報の新規登録処理に失敗しました。");
                throw new \Exception(Config("errors.UPDATE_ERR"));
            }

            ///////////////////////////////
            // 本登録完了のメール送信処理を実行
            ///////////////////////////////
            Mail::send(["text" => "templates.createMember"], [
                "token" => $token,
            ],
            function ($message) use ($post_data) {
                $result = $message
                    ->to($post_data["email"])
                    ->from(Config("env.mail_from_address"))
                    ->cc(Config("env.mail_cc"))
                    ->bcc(Config("env.mail_bcc"))
                    ->subject(Config("const.email.title.COMPLETED_REGISTERING_MEMBER"));
            });
            $failures = Mail::failures();
            if (count($failures) !== 0) {
                throw new \Exception(Config("errors.EMAIL_ERR"));
            }

            logger()->info(__FILE__, $member->toArray());

            return $member;
        } catch (\Exception $e) {
            logger()->error($e);
            return null;
        }
    }

    /**
     * ログイン中ユーザー以外の指定したユーザーIDのユーザー情報を取得
     *
     * @param integer $member_id
     * @param integer $target_member_id
     * @return \App\Models\Member|null
     */
    public static function getMemberInfo(int $member_id, int $target_member_id): ?\App\Models\Member
    {
        try {
            // log
            logger()->info("現在のログイン中ユーザ", ["member_id" => $member_id]);

            // ログイン中ユーザーの存在チェック
            $m = Member::find($member_id);
            if ($m === NULL) {
                throw new \Exception(Config("errors.NOT_FOUND_USER_ERR"));
            }
            $gender = $m->gender;

            // 現在ログイン中のユーザを拒否しているユーザー一覧
            $declined_users = Decline::select("from_member_id")
            ->where("to_member_id", $member_id)
            ->get();

            // log
            logger()->info(__FILE__, $declined_users->toArray());

            // 対象のユーザーが拒否しているユーザー一覧
            $declined_users = array_column($declined_users->toArray(), "from_member_id");

            // ブロックされているかどうかを検証
            if (in_array($target_member_id, $declined_users) === true) {
                logger()->error("現在のユーザー[{$member_id}]は、対象ユーザー[{$target_member_id}]の情報を閲覧できません。");
                return null;
            }

            // 指定したユーザーIDを検索 ただし異性の性別に限る(ログインユーザーと対象ユーザーが同一の場合は､除く)
            if ($member_id === $target_member_id) {
                $member = Member::with([
                    "profile_images",
                    "identity_image",
                    "income_image",
                    "sending_likes",
                    "getting_likes",
                ])
                ->withTrashed()
                ->where("id", $target_member_id)
                ->get()
                ->first();
            } else {
                $member = Member::with([
                    "profile_images",
                    "identity_image",
                    "income_image",
                    "sending_likes",
                    "getting_likes",
                ])
                ->withTrashed()
                ->where("id", $target_member_id)
                ->whereNotIn("gender", [$gender])
                ->get()
                ->first();
            }


            if ($member === NULL) {
                // log
                logger()->error("指定したユーザーID[{$target_member_id}]のユーザー情報の取得に失敗しました。");
                throw new \Exception (Config("errors.NOT_FOUND_ERR"));
            }

            // 本人確認画像をアップ済みの場合
            if (isset($member->identity_image) && Config("const.image.approve_type.authenticated")){
                // 処理
            }

            // 収入証明書がアップ済みの場合
            if (isset($member->income_image) && Config("const.image.approve_type.authenticated")){
                // 処理
            }

            logger()->info(__FILE__, $member->toArray());

            // レスポンスの内容
            return $member;
        } catch (\Throwable $e) {
            // var_dump($e->getLine());
            logger()->error($e);
            return null;
        }
    }

    /**
     * URLパラメータに指定されたIDのユーザー情報を更新
     *
     * @param BaseMemberRequest $request
     * @param integer $member_id
     * @return void
     */
    public static function update(BaseMemberRequest $request, int $member_id)
    {
        try {
            // postデータを取得
            $post_data = $request->validated();

            // log
            logger()->info(__FILE__, $post_data);

            $member = Member::find($post_data["member_id"]);

            // ユーザー情報が見つからない場合
            if ($member === NULL) {
                logger()->error(__FILE__, $post_data);
                throw new \Exception(Config("errors.NOT_FOUND_USER_ERR"));
            }

            // パスワードが変更されている場合のみ
            if (strlen($request->password) > 0) {
                $post_data["password"] = password_hash($post_data["password"], PASSWORD_BCRYPT);
            }
            // Good時メール送信
            if ($request->input("notification_good") === NULL) {
                $post_data["notification_good"] = 0;
            }
            // メッセージ受信時メール送信
            if ($request->input("notification_message") === NULL) {
                $post_data["notification_message"] = 0;
            }
            $result = $member->fill($post_data)->save();

            // 更新用SQLの戻り値チェック
            if ($result !== true) {
                logger()->error(__FILE__, $post_data);
                logger()->error(__FILE__, ["member_id" => $member_id]);
                logger()->error("指定したID[{$member_id}]のプロフィール情報の更新に失敗しました。");
                throw new \Exception(Config("errors.UPDATE_ERR"));
            }

            logger()->info(__FILE__, $member->toArray());
            logger()->info("指定したユーザーID[{$member_id}]のユーザー情報のアップデートが完了しました。");

            return $member;
        } catch (\Throwable $e) {
            // log
            logger()->error($e);
            return null;
        }
    }

    /**
     * ログイン認証
     *
     * @param BaseMemberRequest $request
     * @return Object
     */
    public static function authenticate(BaseMemberRequest $request)
    {
        try {
            // バリデーション済みのpostデータを取得
            $post_data = $request->validated();

            $member = Member::where("email", $post_data["email"])
            // 本登録完了済み
            ->where("is_registered", Config("const.binary_type.on"))
            ->get()
            ->first();

            // Memberレコードの検証
            if ($member === NULL) {
                logger()->info("指定したメールアドレス[{$post_data["email"]}]のユーザーが存在しません。");
                throw new \Exception(Config("errors.AUTH_ERR"));
            }

            // パスワードハッシュの参照
            if (\password_verify($post_data["password"], $member->password) !== true) {
                logger()->error("パスワードの認証に失敗しました。");
                logger()->error(__FILE__, $member->toArray());
                throw new \Exception(Config("errors.AUTH_ERR"));
            }

            // パスワードが一致した場合、新規でセキュリティトークンを生成
            $security_token = RandomToken::MakeRandomToken(128, "SECURITY_");
            $update_data = [
                "security_token" => $security_token,
                "last_login" => (new \DateTime())->format("Y-n-j H:i:s"),
            ];

            $result = $member->fill($update_data)->save();

            // log
            logger()->info(__FILE__, $member->toArray());

            // 会員のログイン履歴をテーブルに残す
            $member_log = MemberLog::create([
                "member_id" => $member->id,
                "login" => Config("const.binary_type.on"),
                "http_user_agent" => $request->server()["HTTP_USER_AGENT"]
            ]);
            logger()->info(__FILE__, $member_log->toArray());

            return $member;
        } catch (\Throwable $e) {
            logger()->error($e);
            return null;
        }
    }

    /**
     * ログアウト処理を実行
     *
     * @param BaseMemberRequest $request
     * @return boolean
     */
    public static function logout(BaseMemberRequest $request):bool
    {
        try {

            // 会員のログアウト履歴をテーブルに残す
            $member_log = MemberLog::create([
                "member_id" => $request->member->id,
                "logout" => Config("const.binary_type.on"),
                "http_user_agent" => $request->server()["HTTP_USER_AGENT"]
            ]);

            // ログアウト履歴のログ
            if ($member_log !== NULL) {
                logger()->info(__FILE__, $member_log->toArray());
            } else {
                logger()->error("ログアウト履歴の保存ができませんでした｡");
            }

            // セッションを全て破棄する
            $request->session()->flush();

            return true;
        } catch (\Throwable $e) {
            logger()->error($e);
            return false;
        }
    }

    /**
     * 既存ユーザーのメールアドレス変更前に
     * 新規メールアドレスをemail_resetsテーブルに登録する
     *
     * @param MemberRequest $request
     * @param string $member_id
     * @return Response
     */
    public static function email(BaseMemberRequest $request, string $member_id): ?EmailReset
    {
        try {
            $post_data = $request->validated();
            logger()->info($post_data);

            // 新規メールアドレスが、membersテーブルに存在しないことを確約する
            $temp = Member::where("email", $post_data["email"])->get()->first();
            if ($temp !== NULL) {
                logger()->error("既に、使用済みのメールアドレス[{$post_data["email"]}に変更しようとしています。");
                throw new \Exception(Config("errors.INTERNAL_ERR"));
            }

            // ログインユーザーの情報取得
            $member = Member::find($post_data["member_id"]);

            if ($member === NULL) {
                logger()->error($post_data);
                logger()->error("メールアドレス変更時、ユーザーID[{$post_data["member_id"]}]のユーザー情報が見つかりませんでした 。");
                throw new \Exception(Config("errors.NOT_FOUND_ERR"));
            }

            // 新規メールアドレスと旧メールアドレスが異なること!
            if ($member->email === $post_data["email"]) {
                logger()->error("新アドレス => ".$post_data["email"]);
                logger()->error("旧アドレス => ".$member->email);
                logger()->error("新旧のアドレスに変更がありません。");
                throw new \Exception(Config("errors.NOT_FOUND_ERR"));
            }

            // メールアドレス変更用トークンとその有効期限を追加
            $post_data["token"] = RandomToken::MakeRandomToken(128, "EMAIL_CHANGE_");
            // 有効期限は24時間とする
            $post_data["expired_at"] = date("Y-n-j H:i:s", time() + 60 * 60 * 24);
            $post_data["is_used"] = Config("const.binary_type.off");

            // 過去にメール変更処理が走っている場合は全て削除する
            $member = EmailReset::where("member_id", $post_data["member_id"])->delete();

            // 新規メールアドレスの登録
            $email_reset = EmailReset::create($post_data);

            // 新規メールリセットの作成に失敗した場合
            if ($email_reset === NULL) {
                throw new \Exception(Config("errors.CREATE_ERR"));
            }
            // log
            logger()->info(__FILE__, $email_reset->toArray());

            $url = action("Member\\IndexController@completedEmail", [
                "token" => $post_data["token"]
            ]);
            // DBへ新規メールアドレスの登録が完了した後メール送信をする
            Mail::send(["text" => "templates.updating_email"], [
                "url" => $url,
            ],
            function ($message) use ($post_data) {
                $result = $message
                    ->to($post_data["email"])
                    ->from(Config("env.mail_from_address"))
                    ->cc(Config("env.mail_cc"))
                    ->bcc(Config("env.mail_bcc"))
                    ->subject(Config("const.email.title.COMPLETED_SENDING_URL_TO_UPDATE_EMAIL"));
            });
            $failures = Mail::failures();
            if (count($failures) !== 0) {
                throw new \Exception(Config("errors.EMAIL_ERR"));
            }

            return $email_reset;
        } catch(\Throwable $e) {
            logger()->error($e);
            return null;
        }
    }


    /**
     * メールアドレス変更用URLをリクエストした際に､既存メールアドレスを変更後メールアドレスに更新する
     *
     * @param BaseMemberRequest $request
     * @param string $token
     * @return Member|null
     */
    public static function completeUpdatingEmail(BaseMemberRequest $request, string $token): ?Member
    {
        try {
            DB::beginTransaction();

            // 同一トークンで､未使用のレコードを取得
            $email_reset = EmailReset::where("token", $request->token)
            ->where("is_used", Config("const.binary_type.off"))
            ->where("expired_at", ">=", (new \DateTime())->format("Y-m-j H:i:s"))
            ->get()
            ->first();
            if ($email_reset === NULL) {
                throw new \Exception("URLトークンが不正です｡");
            }
            // log
            logger()->info(__FILE__, $email_reset->toArray());

            $member = Member::find($email_reset->member_id);
            if ($member === NULL) {
                logger()->error("指定したユーザーが見つかりません｡", [
                    "member_id" => $email_reset->member_id
                ]);
                throw new \Exception(Config("errors.NOT_FOUND_USER_ERR"));
            }

            // メールアドレスの重複状態を検証する
            $temp = Member::where("email", $email_reset->email)
            ->where("id", "!=", $member->id)
            ->get()
            ->first();
            if ($temp !== NULL) {
                throw new \Exception(Config("errors.FAILED_UPDATING_EMAIL_ERR"));
            }

            // 指定したログインユーザーのメールアドレスをアップデート
            $result = $member->fill([
                "email" => $email_reset->email,
            ])->save();

            // 更新処理の成功有無
            if ($result !== true) {
                logger()->error("指定したメールアドレスへの変更処理に失敗しました｡");
                throw new \Exception(Config("errors.FAILED_UPDATING_EMAIL_ERR"));
            }

            // email_resetsテーブルを更新する
            $result = $email_reset->fill([
                "is_used" => Config("const.binary_type.on")
            ])->save();

            // クエリの実行結果検証
            if ($result !== true) {
                logger()->error("メールアドレスのアップデートには成功したが､email_resetsテーブルの更新に失敗しました｡");
                throw new \Exception(Config("errors.FAILED_UPDATING_EMAIL_ERR"));
            }

            DB::commit();
            return $member;
        } catch (\Throwable $e) {
            DB::commit();
            logger()->error($e);
            return null;
        }
    }

    /**
     * 現在ログイン中ユーザーの詳細なプロフィール情報を取得する
     *
     * @param integer $member_id
     * @return Member
     */
    public static function getSelfInfo(int $member_id) : ?Member
    {
        try {
            $member = Member::with([
                "profile_images",
                "identity_image",
                "income_image",
                "sending_likes",
                "getting_likes",
                "price_plan",
            ])
            ->where("id", $member_id)
            ->where("is_registered", Config("const.binary_type.on"))
            ->get()
            ->first();

            // NULLチェック
            if ($member === NULL) {
                logger()->error("指定したユーザーID[{$member_id}]のユーザーが見つかりません。");
                throw new \Exception(Config("errors.NOT_FOUND_USER_ERR"));
            }
            return $member;
        } catch (\Throwable $e) {
            logger()->error($e);
            return null;
        }
    }


    /**
     * 現在ログイン中ユーザーの住居エリア住んでいる異性一覧を取得する
     *
     * @param integer $member_id
     * @param array $users_declining_you
     * @return \Illuminate\Database\Eloquent\Collection|null
     */
    public static function getRecommendedUsers(int $member_id, array $users_declining_you = []) : ?\Illuminate\Database\Eloquent\Collection
    {
        try {
            // ログイン中ユーザーの住居エリア
            $login_user = Member::findOrFail($member_id);

            // log
            logger()->info(__FILE__, $login_user->toArray());

            // 同じ都道府県に住む異性ユーザー一覧
            $members = Member::with([
                "profile_images",
                "identity_image",
                "income_image",
                "sending_likes",
                "getting_likes",
                // "matching_users",
            ])
            // 異性ユーザーのみ取得
            ->whereNotIn("gender", [$login_user->gender])
            // 自身を拒否しているユーザーを除く
            ->whereNotIn("id", $users_declining_you)
            ->where("prefecture", $login_user->prefecture)
            ->where("is_registered", Config("const.binary_type.on"))
            ->orderBy("id", "desc")
            ->get();

            // NULLチェック
            if ($members->count() === 0) {
                logger()->info("ユーザーID[{$login_user->member_id}]と同じ地域のユーザーはいません。");
            }

            // 件数が0でもCollectionオブジェクトが返却される
            return $members;
        } catch (\Throwable $e) {
            logger()->error($e);
            return null;
        }
    }

    /**
     * 指定したクレジットサーバー側IDの有料プランの解約処理を実行
     *
     * @param string $credit_id
     * @return boolean
     */
    public static function unsubscribe (string $credit_id): bool
    {
        try {
            DB::beginTransaction();
            // 解約処理実行日時
            $today = (new \DateTime())->format("Y-n-j H:i:s");
            $member = Member::where("credit_id", $credit_id)
            ->get()
            ->first();

            // NULLチェック
            if ($member === NULL) {
                logger()->error("指定されたcredit_id[{$credit_id}]の会員情報が存在しません｡", ["credit_id" => $credit_id]);
                throw new \Exception("指定されたcredit_id[{$credit_id}]の会員情報が存在しません｡");
            }

            // 有料プラン解約､事前チェック
            $canceled_log = CanceledLog::where([
                "member_id" => $member->id,
                "credit_id" => $credit_id,
            ])
            ->get()
            ->first();

            // NULLチェック
            if ($canceled_log !== NULL) {
                logger()->error("リクエストのあった解約処理リクエストは既に実行ずみです｡", ["credit_id" => $credit_id, "member_id" => $member->id]);
                throw new \Exception("既に､解約処理が実行されています｡");
            }

            // 解約ログテーブルに履歴を保存
            $canceled_log = CanceledLog::create([
                "member_id" => $member->id,
                "credit_id" => $credit_id,
            ]);
            // NULLチェック
            if ($canceled_log === NULL) {
                throw new \Exception("会員ユーザー[{$member->id}]のcredit_idの解約ログ保存に失敗しました｡");
            }

            // membersテーブルから有料フラグをオフにする
            $memo = $member->memo . "{$today}に､credit_id[$credit_id]の有料プランの解約処理を実行完了".PHP_EOL;
            $result = $member->fill([
                "credit_id" => NULL,
                "memo" => $memo,
            ])->save();

            if ($result !== true) {
                throw new \Exception("会員ユーザー[{$member->id}]のcredit_id[{$credit_id}]をNULL化できませんでした｡");
            }

            // commit確定
            DB::commit();
            return true;
        } catch (\Throwable $e) {
            DB::rollback();
            logger()->error("membersテーブルのcredit_idのNULL化に失敗");
            logger()->error($e->getMessage());
            return false;
        }
    }

    /**
     * 第一引数に指定した､ユーザーへ第二引数の異性リストからのメッセージ履歴を取得する
     *
     * @param integer $member_id
     * @param array $matching_users
     * @param integer $limit
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public static function getTalkableUsers(int $member_id, array $matching_users, int $limit = 20): \Illuminate\Pagination\LengthAwarePaginator
    {
        $newest_images = DB::table("images")
        ->select([
            DB::raw("min(id) as id"),
            "member_id",
        ])
        ->where("deleted_at", NULL)
        ->where("use_type", Config("const.image.use_type.profile"))
        ->whereIn("member_id", $matching_users)
        ->groupBy([
            "member_id",
        ])
        ->get();
        $image_id_list = array_column($newest_images->toArray(), "id");

        $matching_users = Member::select([
            "members.id as member_id",
            "members.display_name",
            "images.id as image_id",
            "images.token",
            // 収入証明書カラムが設定されている場合
            DB::raw("(case when income_images.id > 0 then 1 else 0 end) as income"),
            DB::raw("max(timelines.created_at) as timeline_created_at"),
        ])
        ->leftJoin("timelines", function($join) use ($member_id) {
            $join->on("members.id", "=", "timelines.from_member_id")->where("to_member_id", $member_id);
        })
        ->leftJoin("images", function ($join) use ($image_id_list) {
            $join->on("members.id", "=", "images.member_id")
            ->whereIn("images.id", $image_id_list)
            ->where("images.use_type", Config("const.image.use_type.profile"))
            ->orderBy("images.id", "desc");
        })
        ->leftJoin("images as income_images", function ($join) {
            $join->on("members.id", "=", "income_images.member_id")
            ->where("income_images.use_type", Config("const.image.use_type.income"))
            ->where("income_images.is_approved", Config("const.image.approve_type.authenticated"))
            ->orderBy("income_images.id", "desc");
        })
        ->whereIn("members.id", $matching_users)
        ->groupBy([
            "members.id",
            "members.display_name",
            "images.id",
            "images.token",
            "income"
        ])
        ->orderBy("income", "desc")
        ->orderBy("timeline_created_at", "desc")
        ->paginate($limit);
        // print_r($matching_users->toArray());
        return $matching_users;
    }
}
