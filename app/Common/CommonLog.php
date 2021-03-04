<?php

namespace App\Common;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\Log;

class CommonLog
{


    /**
     * 指定したユーザーへのアクションログを取得する
     *
     * @param integer $member_id
     * @param array $excluded_users
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public static function getLogs(int $member_id, array $excluded_users = []) :  \Illuminate\Pagination\LengthAwarePaginator
    {
        $logs = Log::select([
            "from_member_id",
            "to_member_id",
            "action_id",
            DB::raw("max(id) as id"),
            DB::raw("max(created_at) as created_at")
        ])->with([
            "from_member",
            "to_member",
        ])
        ->where("to_member_id", $member_id)
        ->whereNotIn("from_member_id", $excluded_users)
        ->whereNotIn("to_member_id", $excluded_users)
        ->whereHas("from_member")
        ->whereHas("to_member")
        ->orderBy(DB::raw("max(id)"), "desc")
        ->orderBy(DB::raw("max(created_at)"), "desc")
        ->groupBy([
            "from_member_id",
            "to_member_id",
            "action_id",
        ])
        ->paginate(Config("const.limit"));
        // ->offset($offset)
        // ->limit($limit)
        // ->get();

        // good通知または、メッセージの受信とでURLを変更する
        // foreach ($logs->getIterator() as $key => $value) {

        //     // メッセージ通知の場合はチャットルームURL
        //     if ($value->action_id === Config("const.action.message")) {
        //         $value->url = action("Member\\MessageController@talk", [
        //             "to_member_id" => $value->from_member->id,
        //         ]);
        //     } else {
        //         $value->url = action("Member\\IndexController@opponent", [
        //             "target_member_id" => $value->from_member->id,
        //         ]);
        //     }
        // }

        return $logs;
    }

    // /**
    //  * 指定したユーザーへのアクションログを取得する
    //  *
    //  * @param integer $member_id
    //  * @param array $excluded_users
    //  * @return \Illuminate\Pagination\LengthAwarePaginator
    //  */
    // public static function getMessageLogs(int $member_id, array $excluded_users = []) :  \Illuminate\Pagination\LengthAwarePaginator
    // {
    //     $logs = Log::select([
    //         "from_member_id",
    //         "to_member_id",
    //         DB::raw("max(created_at) as created_at")
    //     ])
    //     ->with([
    //         "from_member",
    //         "from_member.getting_likes",
    //         "from_member.sending_likes",
    //         "from_member.profile_images",
    //         "from_member.to_timelines",
    //     ])
    //     ->where("to_member_id", $member_id)
    //     ->where("action_id", Config("const.action.message"))
    //     ->whereNotIn("from_member_id", $excluded_users)
    //     ->whereNotIn("to_member_id", $excluded_users)
    //     ->orderBy(DB::raw("max(created_at)"), "desc")
    //     ->groupBy([
    //         "from_member_id",
    //         "to_member_id",
    //     ])
    //     ->paginate(Config("const.limit"));
    //     // ->offset($offset)
    //     // ->limit($limit)
    //     // ->get();

    //     // good通知または、メッセージの受信とでURLを変更する
    //     foreach ($logs->getIterator() as $key => $value) {
    //         // メッセージ通知の場合はチャットルームURL
    //         if ($value->action_id === Config("const.action.message")) {
    //             $value->url = action("Member\\MessageController@talk", [
    //                 "to_member_id" => $value->from_member->id,
    //             ]);
    //         } else {
    //             $value->url = action("Member\\IndexController@opponent", [
    //                 "target_member_id" => $value->from_member->id,
    //             ]);
    //         }
    //     }

    //     return $logs;
    // }



    /**
     * 未確認のお知らせ一覧を取得する
     *
     * @param integer $member_id
     * @param array $excluded_users
     * @return array
     */
    public static function uncheckLogs(int $member_id, array $excluded_users = []):array
    {
        try {
            $logs = Log::where("to_member_id", $member_id)
            ->where("is_browsed", Config("const.binary_type.off"))
            ->whereNotIn("from_member_id", $excluded_users)
            ->whereNotIn("to_member_id", $excluded_users)
            ->get();

            $uncheck_logs = array_column($logs->toArray(), "id");
            return $uncheck_logs;
        } catch (\Throwable $e) {
            logger()->error($e);
            return [];
        }
    }

    /**
     * 未読のお知らせ一覧を全て既読にする
     *
     * @param integer $member_id
     * @return boolean
     */
    public static function checkLogs(int $member_id):bool
    {
        try {
            // 未読一覧を取得する
            $logs = Log::where("is_browsed", Config("const.binary_type.off"))
            ->where("to_member_id", $member_id)
            ->update(["is_browsed" => Config("const.binary_type.on")]);

            logger()->info($logs);

            return $logs;
        } catch (\Throwable $e) {
            logger()->error($e);
            return false;
        }
    }
}
