<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\BaseMessageRequest;
use App\Models\Room;
use App\Models\Member;
use App\Common\CommonLike;
use App\Common\CommonLog;
use App\Common\CommonDecline;
use App\Common\CommonMember;
use App\Library\Logger;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class MessageController extends Controller
{



    /**
     * メッセージのやり取りが可能なユーザー一覧を取得
     *
     * @param BaseMessageRequest $request
     * @return void
     */
    public function index(BaseMessageRequest $request)
    {
        try {
            // もらったGood
            $matching_users = CommonLike::getMatchingUsers($request->member->id, $request->excluded_users);
            // NULLチェック
            if (isset($matching_users) !== true) {
                throw new \Exception("現在マッチング済みユーザーはいません｡");
            }
            $id_list = array_column($matching_users->toArray(), "id");
            $matching_users = CommonMember::getTalkableUsers($request->member->id, $id_list);

            // print_r($matching_users->toArray());

            Logger::info("__FILE__", "マッチング中ユーザー");
            Logger::info(__FILE__, $matching_users);
            Logger::info("__FILE__", "除外ユーザー");
            Logger::info(__FILE__, $request->excluded_users);

            // // NULLチェック
            // if ($matching_users === NULL) {
            //     throw new \Exception(Config("errors.NOT_FOUND_MATCHING_USERS_ERR"));
            // }
            return view("member.message.index", [
                "request" => $request,
                "matching_users" => $matching_users,
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
     * 現在マッチしていてトークが可能なメッセージページを表示
     *
     * @param BaseMessageRequest $request
     * @return void
     */
    public function talk (BaseMessageRequest $request, int $to_member_id)
    {
        try {
            // トークの利用条件を満たさない場合
            if ($request->talkable !== true) {
                // throw new \Exception("利用可能条件を満たしていません｡");
                return view("member.message.error", [
                    "request" => $request,
                ]);
            }

            $member = CommonMember::getMemberInfo($request->member->id, $to_member_id);
            // 何らかの理由でユーザーが見つからない
            if ($member === NULL) {
                logger()->error("指定したユーザーが見つかりません。");
                throw new \Exception(Config("errors.NOT_FOUND_USER_ERR"));
            }

            return view("member.message.talk", [
                "request" => $request,
                "member" => $member,
            ]);
        } catch (\Throwable $e) {
            logger()->error($e);
            return view("member.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }
}
