<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\BaseMemberRequest;
use App\Http\Requests\BaseEmailRequest;
use App\Common\CommonEmail;
use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\PasswordReissue;
use App\Library\RandomToken;
use App\Library\Logger;
class EmailController extends Controller
{


    /**
     * Emailによる仮登録画面
     *
     * @param BaseEmailRequest $request
     * @return void
     */
    public function index(BaseEmailRequest $request)
    {
        try {
            return view("member.email.index", [
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
     * メールアドレスの仮登録処理を実行する
     *
     * @param MemberRequest $request
     * @return Response
     */
    public function register(BaseEmailRequest $request)
    {
        try {
            // postデータの取得
            $input_data = $request->validated();

            logger()->info($input_data);

            // Emailアドレスの仮登録処理を実行
            $member = CommonEmail::Register($request);
            logger()->info($member);

            if ($member === NULL) {
                Logger::error(__FILE__, "仮登録処理に失敗しました。");
                throw new \Exception(Config("errors.CREATE_TEMP_ACCOUNT_ERR"));
            }

            if ($member !== NULL) {
                return redirect()->action("Member\\EmailController@completed");
            }
            logger()->error($member);
            throw new \Exception(Config("errors.CREATE_ERR"));
        } catch (\Throwable $e) {
            logger()->error($e);
            return view("member.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }

    /**
     * メールアドレスの仮登録完了画面の表示
     *
     * @param BaseEmailRequest $request
     * @return void
     */
    public function completed(BaseEmailRequest $request)
    {
        try {
            logger()->info("メールアドレスによる仮登録完了");
            return view("member.email.completed", [
                "request" => $request,
            ])->withHeaders([
                "Cache-Control" => "no-store, no-cache, must-revalidate, private",
                "Pragma" => "no-cache",
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
     * パスワード再発行用メールアドレス入力フォーム
     *
     * @param Request $request
     * @return Response
     */
    public function reissue(Request $request)
    {
        try {
            return view("member.email.reissue", [
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
     * パスワードリセット用URLの再発行
     *
     * @param BaseEmailRequest $request
     * @return Response
     */
    public function postReissue(BaseEmailRequest $request)
    {
        try {
            $password_reissue = CommonEmail::SendPasswordReissueUrl($request->email, "templates.postReissue");
            return view("member.email.reissueCompleted", [
                "request" => $request,
                "password_reissue" => $password_reissue,
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
