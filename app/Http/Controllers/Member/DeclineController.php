<?php

namespace App\Http\Controllers\Member;

use Illuminate\Http\Request;
use App\Http\Requests\BaseDeclineRequest;
use App\Http\Controllers\Controller;
use App\Models\Decline;
use App\Common\CommonDecline;

class DeclineController extends Controller
{
    /**
     * ログイン中ユーザーがブロックしているユーザー一覧を取得する
     *
     * @param Request $request
     * @return void
     */
    public function index(BaseDeclineRequest $request)
    {
        try {
            // 現在、ログインユーザーがブロックしているユーザー一覧
            $declining_users = CommonDecline::getUsersYouDeclining($request->member->id);

            if ($declining_users->total() === 0) {
                throw new \Exception("ブロック済みユーザーはいません｡");
            }
            // log
            logger()->info($declining_users);

            return view("member.decline.index", [
                "request" => $request,
                "declining_users" => $declining_users,
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
     * 指定したユーザーをブロックする
     *
     * @param BaseDeclineRequest $request
     * @return void
     */
    public function block(BaseDeclineRequest $request)
    {
        try {
            // ユーザーのブロック処理を実行
            $decline = CommonDecline::block($request->from_member_id, $request->to_member_id);

            if ($decline === NULL) {
                throw new \Exception("ブロックに失敗しました｡");
            }

            // log
            logger()->info($decline);

            // // ブロックしたユーザーの詳細ページへリダイレクト
            // return redirect()->action("Member\\IndexController@opponent", [
            //     "target_member_id" => $request->to_member_id
            // ]);

            // ブロック完了ページへリダイレクト
            return redirect()->action("Member\\DeclineController@completedBlocking");
        } catch (\Throwable $e) {
            logger()->error($e);
            return view("member.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }


    /**
     * 指定したユーザーのブロック完了ページ
     *
     * @param BaseDeclineRequest $request
     * @return void
     */
    public function completedBlocking(BaseDeclineRequest $request)
    {
        try {
            return view("member.decline.completed", [
                "request" => $request,
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
     * 指定したユーザーのブロックを解除する
     *
     * @param Request $request
     * @return void
     */
    public function unblock(BaseDeclineRequest $request)
    {
        try {
            $decline = CommonDecline::unblock($request);
            if ($decline !== true) {
                logger()->error("ブロック解除処理に失敗しました。", $request->validated());
            }
            // ブロック解除後は､マイページへ遷移させる
            return redirect()->action("Member\\IndexController@index");

        } catch (\Throwable $e) {
            logger()->error($e);
            return view("member.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }
}
