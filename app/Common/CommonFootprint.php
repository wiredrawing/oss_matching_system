<?php

namespace App\Common;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\BaseMemberRequest;
use App\Models\Member;
use App\Models\Footprint;
use Illuminate\Support\Facades\Mail;

class CommonFootprint
{

    /**
     * 足跡を残す処理
     *
     * @param Request $request
     * @param integer $target_member_id
     * @return Footprint|null
     */
    public static function visit(Request $request, int $target_member_id) : ?Footprint
    {
        try {
            // 自身への足跡は残せない
            if ($request->member->id === $target_member_id) {
                throw new \Exception("自分自身への足跡は残せません｡");
            }

            // 指定された組み合わせが存在するかどうか
            $footprint = Footprint::where("from_member_id", $request->member->id)
            ->where("to_member_id", $target_member_id)
            ->get()
            ->first();

            // 再来訪の場合
            if ($footprint !== NULL) {
                $access_count = ++$footprint->access_count;
                $result = $footprint->fill(["access_count" => $access_count])->save();
                if ($result !== true) {
                    logger()->error("[{$request->member->id}]から[{$target_member_id}]への足跡の加算に失敗しました。");
                    return NULL;
                }
                return $footprint;
            }

            // 初回来訪の場合
            $footprint = Footprint::create([
                "from_member_id" => $request->member->id,
                "to_member_id" => $target_member_id,
                "access_count" => 1,
            ]);
            if ($footprint === NULL) {
                logger()->error("[{$request->member->id}]から[{$target_member_id}]への足跡の登録に失敗しました。");
                return NULL;
            }
            return $footprint;
        } catch (\Throwable $e) {
            logger()->error($e);
            return null;
        }
    }

    /**
     * 引数に指定されたユーザーへの足跡一覧を取得する
     *
     * @param integer $member_id
     * @param array $excluded_users
     * @return Illuminate\Pagination\LengthAwarePaginator
     */
    public static function getFootprints(int $member_id, array $excluded_users = []): \Illuminate\Pagination\LengthAwarePaginator
    {
        // 指定されたユーザーを閲覧したユーザーIDの一覧を取得する
        $footprints = Footprint::select("from_member_id")
        ->where("to_member_id", $member_id)
        ->get();

        // if ($footprints->count() === 0) {
        //     return NULL;
        // }

        // 来訪者一覧を作成
        $from_users = array_column($footprints->toArray(), "from_member_id");

        // Goodを贈ったユーザー情報を取得
        $from_users = Member::with([
            "getting_likes",
            "sending_likes",
            "profile_images",
            "from_footprints" => function ($query) use  ($member_id) {
                $query->where("to_member_id", $member_id);
            }
        ])
        // 自身のIDを除く
        ->where("id", "!=", $member_id)
        ->whereIn("id", $from_users)
        ->whereNotIn("id", $excluded_users)
        ->orderBy("id","desc")
        ->paginate(Config("const.limit"));
        // ->get();

        // paginatorを返却
        return $from_users;
    }


    /**
     * ログイン中ユーザーがまだ未確認の足跡履歴を取得する (戻り値は必ず配列)
     *
     * @param integer $member_id
     * @param array $excluded_users
     * @return array
     */
    public static function uncheckFootprints(int $member_id, array $excluded_users = []): array
    {
        try {
            $footprints = Footprint::select("id")
            ->where("is_browsed", Config("const.binary_type.off"))
            ->where("to_member_id", $member_id)
            ->whereNotIn("from_member_id", $excluded_users)
            ->whereNotIn("to_member_id", $excluded_users)
            ->get();
            $uncheck_footprints = array_column($footprints->toArray(), "id");

            return $uncheck_footprints;
        } catch (\Throwable $e) {
            logger()->error($e);
            return [];
        }
    }


    /**
     * 未読の足跡一覧を全て既読にする
     *
     * @param integer $member_id
     * @return boolean
     */
    public static function checkFootprints(int $member_id):bool
    {
        try {
            // 未読一覧を取得する
            $footprints = Footprint::where("is_browsed", Config("const.binary_type.off"))
            ->where("to_member_id", $member_id)
            ->update(["is_browsed" => Config("const.binary_type.on")]);

            // 未読を既読にしたlogを保存
            logger()->info(__FILE__." チェック完了済みの未読の足跡データ", [
                "footprints" => $footprints,
            ]);

            return $footprints;
        } catch (\Throwable $e) {
            logger()->error($e);
            return false;
        }
    }
}
