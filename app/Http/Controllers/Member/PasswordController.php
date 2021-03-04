<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\BasePasswordRequest;
use App\Models\PasswordReissue;
use App\Library\Logger;
use Illuminate\Support\Facades\DB;

class PasswordController extends Controller
{
    /**
     * ログイン済みの場合の、パスワード再発行要URLフォーム
     *
     * @param BasePasswordRequest $request
     * @param string $token
     * @return void
     */
    public function index(BasePasswordRequest $request)
    {
        try {
            return view("member.password.index", [
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
     * パスワードの再発行処理
     *
     * @param BasePasswordRequest $request
     * @param string $token
     * @return void
     */
    public function postUpdate(BasePasswordRequest $request)
    {
        try {
            $input_data = $request->validated();

            // log
            Logger::info(__FILE__, $input_data);

            $update_data = [
                "password" => password_hash($input_data["password"], PASSWORD_DEFAULT),
            ];
            // membersテーブrのパスワードを更新する
            $result = $request->member->fill($update_data)->save();

            if ($result !== true) {
                throw new \Exception (Config("errors.UPDATE_ERR"));
            }
            return redirect()->action("Member\\PasswordController@completed");
        } catch (\Throwable $e) {
            DB::rollback();
            logger()->error($e);
            return view("member.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }

    /**
     * パスワード再発行完了画面
     *
     * @param BasePasswordRequest $request
     * @param string $token
     * @return Response
     */
    public function completed(BasePasswordRequest $request)
    {
        return view("member.password.completed", [
            "request" => $request,
        ]);
    }
}
