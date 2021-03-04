<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\LoginRequest;
use App\Http\Controllers\Controller;
use App\Models\Administrator;
use App\Models\AdministratorLog;
use Illuminate\Http\Request;

class LoginController extends Controller
{


    /**
     * ログインフォーム
     *
     * @param Request $request
     * @return void
     */
    public function login(LoginRequest $request)
    {
        try {
            return view("admin.login.index", [
                "request" => $request,
            ]);
        } catch (\Throwable $e) {
            return view("admin.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }

    /**
     * ログイン認証処理
     *
     * @param Request $request
     * @return void
     */
    public function authenticate(LoginRequest $request)
    {
        try {
            // 管理者のログイン処理
            $admin = Administrator::where([
                "email" => $request->email,
            ])->get()
            ->first();

            if ($admin === NULL) {
                throw new \Exception("ユーザーが見つかりません｡");
            }

            if (password_verify($request->password, $admin->password) !== true) {
                throw new \Exception("ユーザーが見つかりません｡");
            }

            // 最終ログインを更新
            $admin->fill([
                "last_login" => (new \DateTime())->format("Y-n-j H:i:s"),
            ])->save();

            $request->session()->put([
                "administrator" => $admin,
            ]);

            // 管理用テーブルにログイン履歴を残す
            $administrator_log = AdministratorLog::create([
                "administrator_id" => $admin->id,
                "http_user_agent" => $request->server()["HTTP_USER_AGENT"],
                "login" => Config("const.binary_type.on"),
            ]);
            logger()->info(__FILE__, $administrator_log->toArray());
            return redirect()->action("Admin\\MemberController@index");
        } catch (\Throwable $e) {
            return view("admin.errors.index", [
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
    public function logout(Request $request)
    {
        try {
            $request->session()->flush();

            // 管理用テーブルにログアウト履歴を残す
            $administrator_log = AdministratorLog::create([
                "administrator_id" => $request->administrator->id,
                "http_user_agent" => $request->server()["HTTP_USER_AGENT"],
                "logout" => Config("const.binary_type.on"),
            ]);
            logger()->info(__FILE__, $administrator_log->toArray());
            return redirect()->action("Admin\\LoginController@login");
        } catch (\Throwable $e) {
            return view("admin.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }
}
