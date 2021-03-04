<?php

namespace App\Common;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\BaseMemberRequest;
use App\Http\Requests\BaseDeclineRequest;
use App\Models\Member;
use App\Models\Decline;
use Illuminate\Support\Facades\Mail;

class CommonDecline
{

    /**
     * 指定したユーザーをブロックする
     *
     * @param integer $from_member_id
     * @param integer $to_member_id
     * @return Decline|null
     */
    public static function block(int $from_member_id, int $to_member_id): ?Decline
    {
        try {
            // 自分自身をブロックすることはできない
            if ($from_member_id === $to_member_id) {
                throw new \Exception("自分自身をブロックすることはできません｡");
            }
            // 既にブロックされていないかをチェック
            $decline = Decline::where([
                "from_member_id" => $from_member_id,
                "to_member_id" => $to_member_id,
            ])
            ->get()
            ->first();

            // NULLチェック
            if ($decline !== NULL) {
                logger()->info("ID[{$from_member_id}]からID[{$to_member_id}]は既にブロックされています。");
                return $decline;
            }

            // 新規ブロックの組み合わせ
            $insert_data = [
                "to_member_id" => $to_member_id,
                "from_member_id" => $from_member_id,
            ];

            $declined_user = Decline::create($insert_data);

            if ($declined_user !== NULL) {
                logger()->info(__FILE__, $declined_user->toArray());
            } else {
                // エラーログを出力
                logger()->error("指定した異性のブロックに失敗しました｡", [
                    "from_member_id" => $from_member_id,
                    "to_member_id" => $to_member_id,
                ]);
            }
            return $declined_user;
        } catch (\Throwable $e) {
            logger()->error($e);
            return NULL;
        }
    }


    /**
     * 指定したユーザーが現在ブロックしているユーザー一覧を取得
     *
     * @param integer $member_id
     * @param integer $limit
     * @return \Illuminate\Pagination\LengthAwarePaginator|null
     */
    public static function getUsersYouDeclining(int $member_id, int $limit = 0) :  ?\Illuminate\Pagination\LengthAwarePaginator
    {
        try {
            if ($limit === 0) {
                $limit = Config("const.limit");
            }
            // ログインユーザーがブロックしているユーザー一覧を取得
            $declined_users = Decline::select("to_member_id")->where("from_member_id", $member_id)->get();
            // log
            logger()->info("ログインユーザーがブロック中のユーザー一覧", $declined_users->toArray());

            $declined_users = array_column($declined_users->toArray(), "to_member_id");
            // log
            logger()->info(__FILE__, $declined_users);

            // 拒否されたユーザー一覧の情報を取得する
            $members = Member::with([
                "getting_likes",
                "sending_likes",
                "profile_images",
            ])
            ->where("is_registered", Config("const.binary_type.on"))
            ->whereIn("id", $declined_users)
            ->paginate($limit);

            // log
            logger()->info(__FILE__, $members->toArray());

            return $members;
        } catch (\Throwable $e) {
            logger()->error($e);
            return null;
        }
    }

    /**
     * 指定したユーザーのブロックを解除する
     *
     * @param BaseDeclineRequest $request
     * @return boolean
     */
    public static function unblock(BaseDeclineRequest $request) : bool
    {
        try {
            $decline = Decline::where("from_member_id", $request->from_member_id)
                ->where("to_member_id", $request->to_member_id)
                ->get()
                ->first();
            if ($decline === NULL) {
                logger()->error("当該の組み合わせのレコードが見つかりません。");
                logger()->error("ログインユーザー[{$request->member_id}]から異性ユーザー[{$request->target_member_id}]のブロック解除に失敗しました。");
                return false;
            }
            $result = $decline->delete();
            return $result;
        } catch (\Throwable $e) {
            logger()->error($e);
            return false;
        }
    }


    /**
     * 現在、ログインユーザーを拒否しているユーザー一覧を取得する
     *
     * @param integer $member_id
     * @return void
     */
    public static function getUsersDecliningYou(int $member_id) : ?array
    {
        try {
            // 指定したユーザーをブロックしているユーザーID一覧を取得する
            $members = Decline::select("from_member_id")->where("to_member_id", $member_id)->get();

            // 拒否しているユーザーIDのみを取得
            $members = array_column($members->toArray(), "from_member_id");

            // log
            logger()->info(__FILE__, $members);

            return $members;
        } catch (\Throwable $e) {
            logger()->error($e);
            return [];
        }
    }


    /**
     * 引数に指定したユーザーがブロックしている､あるいはブロックされているユーザー一覧を取得する
     *
     * @param integer $member_id
     * @return array
     */
    public static function getExcludedUsers(int $member_id): array
    {
        try {
            // 除外ユーザー
            $excluded_users = [];

            // 指定したユーザーをブロックしているユーザー一覧
            $from_members = Decline::select("from_member_id")->where("to_member_id", $member_id)->get();

            // log
            logger()->info(__FILE__, $from_members->toArray());

            // from_member_idのみを配列に
            if ($from_members->count() > 0) {
                $from_members = array_column($from_members->toArray(), "from_member_id");
                $excluded_users = array_merge($excluded_users, $from_members);
            }


            // 自身がブロックしているユーザー一覧
            $to_members = Decline::select("to_member_id")->where("from_member_id", $member_id)->get();

            // log
            logger()->info(__FILE__, $to_members->toArray());

            if ($to_members->count() > 0) {
                $to_members = array_column($to_members->toArray(), "to_member_id");
                $excluded_users = array_merge($excluded_users, $to_members);
            }

            // log
            logger()->info(__FILE__, $excluded_users);

            return $excluded_users;
        } catch (\Throwable $e) {
            logger()->error($e);
            return [];
        }
    }
}
