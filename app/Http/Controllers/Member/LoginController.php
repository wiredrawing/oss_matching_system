<?php

namespace App\Http\Controllers\Member;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\BaseMemberRequest;
use App\Common\CommonMember;
use App\Library\Logger;
use App\Models\MemberLog;
class LoginController extends Controller
{

    /**
     * ログイン画面の描画用コントローラー
     *
     * @param Request $request
     * @return Response
     */
    public function index (Request $request)
    {
        try {
            // ログインページのレンダリング
            return view("member.login.index", [
                "request" => $request,
            ]);
        } catch (\Throwable $e) {
            // log
            logger()->error(__FILE__);
            logger()->error($e);
            return view("member.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @return Response
     */
    public function authenticate(BaseMemberRequest $request)
    {
        try {
            // 入力データを取得
            $input_data = $request->validated();

            // log
            Logger::info(__FILE__, $input_data);

            // ユーザー認証処理
            $member = CommonMember::authenticate($request);

            if ($member === NULL) {
                throw new \Exception(Config("errors.FAILED_LOGGING_IN_ERR"));
            }

            $request->session()->put("member", $member);

            return redirect()->action("Member\\IndexController@index");
        } catch (\Throwable $e) {
            // log
            Logger::info(__FILE__, $e);
            return view("member.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }

    /**
     * ログアウト処理
     *
     * @param Request $request
     * @return void
     */
    public function logout(BaseMemberRequest $request)
    {
        try {
            // 共通のログアウト処理を実行
            $result = CommonMember::logout($request);
            // log
            logger()->info(__FILE__);
            logger()->info("ログアウト処理の実行");

            return redirect()->action("Member\\IndexController@index");
        } catch (\Throwable $e) {
            // log
            Logger::info(__FILE__, $e);
            return view("member.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }
}
