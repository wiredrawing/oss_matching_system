<?php

namespace App\Common;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\BaseLikeRequest;
use App\Models\Member;
use App\Models\Like;
use App\Models\Log;

class CommonLike
{

    /**
     * 指定したユーザーが贈ったGood
     *
     * @param integer $member_id
     * @param array $excluded_users
     * @return \Illuminate\Pagination\LengthAwarePaginator|null
     */
    public static function sendingLike(int $member_id, array $excluded_users = []) : ?\Illuminate\Pagination\LengthAwarePaginator
    {
        try {
            // 贈ったGoodを取得
            $likes = Like::select("to_member_id")
            ->join("members", function($join) {
                $join->on("members.id", "=", "likes.to_member_id")
                ->where("members.deleted_at", "=", NULL);
            })
            ->where("from_member_id", $member_id)->get();
            if ($likes === NULL) {
                logger()->info("現在まだGoodを一度も贈っていません。");
            }

            // Goodを贈った異性ユーザーのIDリストを生成
            $to_users = array_column($likes->toArray(), "to_member_id");

            // Goodを贈ったユーザー情報を取得
            $to_users = Member::with([
                "getting_likes",
                "sending_likes",
                "profile_images",
            ])
            ->whereIn("id", $to_users)
            ->whereNotIn("id", $excluded_users)
            ->orderBy("updated_at", "desc")
            ->paginate(Config("const.limit"));
            // ->get();

            // log
            logger()->info(__FILE__, $to_users->toArray());

            return $to_users;
        } catch (\Throwable $e) {
            // var_dump($e->getMessage());
            logger()->error($e);
            return null;
        }
    }


    /**
     * 指定したユーザーがもらったGood
     *
     * @param integer $member_id
     * @param array $excluded_users
     * @return \Illuminate\Pagination\LengthAwarePaginator|null
     */
    public static function gettingLike(int $member_id, array $excluded_users = []) : ?\Illuminate\Pagination\LengthAwarePaginator
    {
        try {
            // 自身がもらったGood一覧を取得
            $likes = Like::select("from_member_id")
            ->join("members", function ($join) {
                $join->on("members.id", "=", "likes.from_member_id")
                ->where("members.deleted_at", "=", NULL);
            })
            ->where("to_member_id", $member_id)->get();

            if ($likes === NULL) {
                logger()->info("現在まだ一度もGoodをもらっていません。");
            }
            // Goodを贈った異性ユーザーのIDリストを生成
            $from_users = array_column($likes->toArray(), "from_member_id");
            $from_users = Member::select("id")->whereIn("id", $from_users)->get();
            $from_users = array_column($from_users->toArray(), "id");


            // Goodを贈ったユーザー情報を取得
            $from_users = Member::with([
                "getting_likes",
                "sending_likes",
                "profile_images",
            ])
            ->whereIn("id", $from_users)
            // 除外ユーザーを指定
            ->whereNotIn("id", $excluded_users)
            ->orderBy("updated_at", "desc")
            ->paginate(Config("const.limit"));
            // ->get();

            // log
            logger()->info(__FILE__, $from_users->toArray());

            return $from_users;
        } catch (\Throwable $e) {
            logger()->error($e);
            return null;
        }
    }


    /**
     * Goodをおくる処理、且つ同時にマッチングが完了した場合
     * そのユーザー通しのトークルームのレコードを作成する
     *
     * @param BaseLikeRequest $request
     * @return Like|null
     */
    public static function sendLike(BaseLikeRequest $request) : ?Like
    {
        try {
            // トランザクション開始
            DB::beginTransaction();
            // 自分自身にいいねは送れない
            if ($request->from_member_id === $request->to_member_id) {
                throw new \Exception("自分自身にGoodを贈ることはできません｡");
            }

            $post_data = $request->validated();
            // log
            logger()->info(__FILE__, $request->validated());

            // リクエストしたGoodアクションの重複検証
            $like = Like::where([
                "from_member_id" => $request->from_member_id,
                "to_member_id" => $request->to_member_id,
            ])
            ->get()
            ->first();

            if ($like !== NULL) {
                logger()->error(__FILE__, $like->toArray());
                throw new \Exception(Config("errors.DUPLICATION_ERR"));
            }

            // 男性から女性、女性から男性が成り立つかどうかを検証
            $from_member = Member::findOrFail($request->from_member_id);
            $to_member = Member::findOrFail($request->to_member_id);

            // 同性チェック
            if ($from_member->gender === $to_member->gender) {
                logger()->error("同性同士のマッチングはできません。");
                logger()->error(__FILE__, $from_member->toArray());
                logger()->error(__FILE__, $to_member->toArray());
                throw new \Exception(Config("errors.INTERNAL_ERR"));
            }

            $like = Like::create([
                "from_member_id" => $request->from_member_id,
                "to_member_id" => $request->to_member_id,
            ]);
            // NULLチェック
            if ($like === NULL) {
                logger()->error("ID[{$post_data["member_id"]}]からID[{$post_data["liked_member_id"]}]へのGoodが失敗しました。");
                throw new \Exception(Config("errors.CREATE_ERR"));
            }
            // log
            logger()->info(__FILE__, $like->toArray());

            // Goodアクションの履歴を保持
            $log = Log::create([
                "from_member_id" => $request->from_member_id,
                "to_member_id" => $request->to_member_id,
                "action_id" => Config("const.action.like"),
            ]);
            // log
            logger()->info(__FILE__, $log->toArray());

            // 両ユーザーがマッチしているかどうか
            $is_match = static::isMatch($request->from_member_id, $request->to_member_id);
            if ($is_match === true) {
                $log = Log::create([
                    "from_member_id" => $request->from_member_id,
                    "to_member_id" => $request->to_member_id,
                    "action_id" => Config("const.action.match"),
                ]);
                $log = Log::create([
                    "from_member_id" => $request->to_member_id,
                    "to_member_id" => $request->from_member_id,
                    "action_id" => Config("const.action.match"),
                ]);
            }
            // DB更新確定
            DB::commit();

            // Goodの宛先ユーザが通知オンの場合
            if ((int)$to_member->notification_good === 1) {
                $notification_email = "";
                if ($is_match === true) {
                    // マッチングが完了したらマッチング完了メールを送信
                    $notification_email = "templates.completed_match";
                } else {
                    // フォローのみなら、フォローメールのみ送信
                    $notification_email = "templates.completed_like";
                }
                // いいねの贈り元ユーザーのプロフィールページ
                $url = action("Member\\IndexController@opponent", [
                    "target_member_id" => $from_member->id,
                ]);
                Mail::send(["text" => $notification_email], [
                        "from_member" => $from_member,
                        "url" => $url,
                    ],
                    function ($message) use ($to_member) {
                        $result = $message
                            ->to($to_member->email)
                            ->from(Config("env.mail_from_address"))
                            ->cc(Config("env.mail_cc"))
                            ->bcc(Config("env.mail_bcc"))
                            ->subject("test");
                    }
                );
                $failures = Mail::failures();
                if (count($failures) !== 0) {
                    throw new \Exception(Config("errors.EMAIL_ERR"));
                }
            }

            return $like;
        } catch (\Throwable $e) {
            DB::rollback();
            logger()->error($e);
            return null;
        }
    }


    /**
     * 指定したカップルの組み合わせがマッチングしているかどうかを検証
     *
     * @param integer $member_id
     * @param integer $target_member_id
     * @return boolean
     */
    public static function isMatch(int $member_id, int $target_member_id): bool
    {
        try {
            $like = Like::where("from_member_id", $member_id)
            ->where("to_member_id", $target_member_id)
            ->get();
            $target_like = Like::where("from_member_id", $target_member_id)
            ->where("to_member_id", $member_id)
            ->get();

            // 双方の組み合わせが1件ずつあればマッチング済み
            if ($like->count() === 1 && $target_like->count() === 1) {
                return true;
            } else {
                return false;
            }
        } catch (\Throwable $e) {
            logger()->error($e);
            return false;
        }
    }

    /**
     * ログイン中ユーザ-が第2引数のユーザーにGoodを贈っているかどうか
     *
     * @param integer $member_id
     * @param integer $target_member_id
     * @return boolean
     */
    public static function isLiking(int $member_id, int $target_member_id) : bool
    {
        try {
            $is_liking = Like::where("from_member_id", $member_id)
            ->where("to_member_id", $target_member_id)
            ->get()
            ->first();

            // NULLチェック
            if ($is_liking === NULL) {
                // 組み合わせが存在しない場合は、ログインユーザーはGoodしていない。
                return false;
            }
            return true;
        } catch (\Throwable $e) {
            logger()->error($e);
            return false;
        }
    }


    /**
     * ログイン中ユーザ-が第2引数のユーザーからGoodされているかどうか
     *
     * @param integer $member_id
     * @param integer $target_member_id
     * @return boolean
     */
    public static function isLiked(int $member_id, int $target_member_id) : bool
    {
        try {
            $is_liked = Like::where("to_member_id", $member_id)
            ->where("from_member_id", $target_member_id)
            ->get()
            ->first();

            // NULLチェック
            if ($is_liked === NULL) {
                // 組み合わせが存在しない場合は、ログインユーザーはGoodしていない。
                return false;
            }
            return true;
        } catch (\Throwable $e) {
            logger()->error($e);
            return false;
        }
    }


    /**
     * 指定したユーザーと相互マッチングしているユーザー一覧を取得する
     *
     * @param integer $member_id
     * @param array $users_declining_you
     * @return \Illuminate\Pagination\LengthAwarePaginator|null
     */
    public static function getMatchingUsers(int $member_id, array $users_declining_you = []) : ? \Illuminate\Database\Eloquent\Collection
    {
        // ログイン中のユーザーがGoodを贈ったリスト
        $sending_likes = Like::select("to_member_id")
        ->join("members", function ($join) {
            $join->on("members.id", "=", "likes.to_member_id")
            ->where("members.deleted_at", "=", NULL);
        })
        ->where("from_member_id", $member_id)
        ->get();
        if ($sending_likes->count() === 0) {
            logger()->info("指定したユーザーがGoodした相手は存在しません｡", ["member_id" => $member_id]);
            // return NULL;
        }
        $to_users = array_column($sending_likes->toArray(), "to_member_id");

        // 上記のリストの中で､ログイン中のユーザーに対してGoodを贈ったリスト
        $getting_likes = Like::select("from_member_id")
        ->join("members", function ($join) {
            $join->on("members.id", "=", "likes.from_member_id")
            ->where("members.deleted_at", "=", NULL);
        })
        ->where("to_member_id", $member_id)
        ->whereIn("from_member_id", $to_users)
        ->get();
        if ($getting_likes->count() === 0) {
            logger()->info("ログインユーザーをGoodしている人はいません｡", ["member_id" => $member_id]);
            // return NULL;
        }

        // 相互マッチングが確定しているユーザー一覧
        $matching_user_id_list = array_column($getting_likes->toArray(), "from_member_id");

        $matching_users = Member::with([
            "getting_likes",
            "sending_likes",
            "profile_images",
            "income_image",
            "to_timelines",
        ])
        ->where("is_registered", Config("const.binary_type.on"))
        // マッチング済みユーザーID
        ->whereIn("id", $matching_user_id_list)
        // ただし、ブロックしているユーザーを除く
        ->whereNotIn("id", $users_declining_you)
        ->orderBy("income_certificate", "desc")
        // ->paginate(Config("const.limit"));
        ->get();
        return $matching_users;
    }


    /**
     * 指定したユーザーに対してのGood履歴を取得する
     *
     * @param integer $member_id
     * @return void
     */
    public static function getMatchingLogs(int $member_id): ?\Illuminate\Database\Eloquent\Collection
    {
        try {
            $likes = Like::with([
                "from_member"
            ])
            ->where("to_member_id", $member_id)
            ->get();
            return $likes;
        } catch (\Throwable $e) {
            logger()->info($e->getMessage());
            logger()->error($e);
            return null;
        }
    }
}
