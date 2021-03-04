<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\BasePasswordRequest;
use App\Models\PasswordReissue;
use Illuminate\Support\Facades\DB;

class PasswordController extends Controller
{
    /**
     * パスワード再発行要URLフォーム
     *
     * @param BasePasswordRequest $request
     * @param string $token
     * @return void
     */
    public function update(BasePasswordRequest $request, string $token)
    {
        try {
            $input_data = $request->validated();
            $token = $input_data["token"];

            $password_reissue = PasswordReissue::with([
                "member",
            ])
            ->where("token", $token)
            ->get()
            ->first();

            return view("password.update", [
                "request" => $request,
                "token" => $token,
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
    public function postUpdate(BasePasswordRequest $request, string $token)
    {
        try {

            DB::beginTransaction();
            $input_data = $request->validated();

            // log
            logger()->info(__FILE__, $input_data);

            $password_reissue = PasswordReissue::with(["member"])
            ->where("token", $input_data["token"])
            ->get()
            ->first();

            // ユーザー情報の取得に失敗した場合
            if ($password_reissue === NULL) {
                throw new \Exception(Config("errors.NOT_FOUND_ERR"));
            }

            // log
            logger()->info(__FILE__, $password_reissue->toArray());

            // memberオブジェクトを取得
            $member = $password_reissue->member;
            $update_data = [
                "password" => password_hash($input_data["password"], PASSWORD_DEFAULT),
            ];
            // membersテーブrのパスワードを更新する
            $result = $member->fill($update_data)->save();

            if ($result !== true) {
                throw new \Exception (Config("errors.UPDATE_ERR"));
            }
            $result = $password_reissue->delete();

            DB::commit();
            return redirect()->action("PasswordController@completed");
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
        return view("password.completed", [
            "request" => $request,
        ]);
    }
}
