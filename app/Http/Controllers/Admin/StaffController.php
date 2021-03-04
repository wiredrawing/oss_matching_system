<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StaffRequest;
use App\Models\Administrator;
use Illuminate\Http\Request;

class StaffController extends Controller
{


    /**
     * 現在登録済みの全管理者アカウントを表示
     *
     * @param Request $request
     * @return void
     */
    public function index(Request $request)
    {
        try {
            $administrators = Administrator::paginate(Config("const.limit"));

            return view("admin.staff.index", [
                "request" => $request,
                "administrators" => $administrators,
            ]);
        } catch (\Throwable $e) {
            return view("admin.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }


    /**
     * 運営者アカウントの新規作成画面
     *
     * @param Request $request
     * @return void
     */
    public function create(Request $request)
    {
        try {
            return view("admin.staff.create", [
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
     * 新規運営者アカウントの作成処理の実行
     *
     * @param StaffRequest $request
     * @return void
     */
    public function postCreate(StaffRequest $request)
    {
        try {
            // 既存アカウントのチェック
            $admin = Administrator::where("email", $request->email)->get()->first();
            if ($admin !== NULL) {
                throw new \Exception("既に､存在する運営者アカウントです｡");
            }
            $post_data = $request->validated();
            $post_data["password"] = password_hash($post_data["password"], PASSWORD_DEFAULT);
            // 新規作成
            $administrator = Administrator::create($post_data);
            // 成功可否チェック
            if ($administrator === NULL) {
                throw new \Exception("新規運営者アカウントの作成に失敗しました｡");
            }
            return redirect()->action("Admin\\StaffController@completed");
        } catch (\Throwable $e) {
            return view("admin.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }

    public function update(StaffRequest $request, int $administrator_id)
    {
        try {
            $administrator = Administrator::find($request->administrator_id);

            return view("admin.staff.update", [
                "request" => $request,
                "administrator" => $administrator,
            ]);
        } catch (\Throwable $e) {
            return view("admin.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }

    /**
     * 指定した運営者アカウントの更新処理を実行する
     *
     * @param StaffRequest $request
     * @param integer $administrator_id
     * @return void
     */
    public function postUpdate(StaffRequest $request, int $administrator_id)
    {
        try {
            $post_data = $request->validated();

            // パスワード変更時のみDBを更新する
            if (array_key_exists("password", $post_data)) {
                $post_data["password"] = password_hash($post_data["password"], PASSWORD_DEFAULT);
            }

            $administrator = Administrator::find($request->administrator_id);
            $result = $administrator->fill($post_data)->save();

            if ($result !== true) {
                throw new \Exception("指定した運営者アカウントの更新処理が失敗しました｡");
            }

            return redirect()->action("Admin\\StaffController@index");
        } catch (\Throwable $e) {
            return view("admin.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }

    public function completed(Request $request)
    {
        try {
            return view("admin.staff.completed", [
                "request" => $request,
            ]);
        } catch (\Throwable $e) {
            return view("admin.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }
}
