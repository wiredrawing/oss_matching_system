<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\BaseViolationRequest;
use App\Models\Member;
use App\Models\ViolationCategory;
use App\Models\ViolationReport;
use App\Common\CommonDecline;
use Illuminate\Http\Request;

class ViolationController extends Controller
{



    /**
     * 指定したユーザーに対する通報処理入力画面
     *
     * @param BaseViolationRequest $request
     * @param integer $member_id
     * @return void
     */
    public function create(BaseViolationRequest $request, int $member_id)
    {
        try {
            // 自分自身を通報することはできない
            if ($request->member->id === $member_id) {
                throw new \Exception("自分自身を通報することはできません｡");
            }
            // 抵触違反内容
            $violation_list = Config("const.violation_list");
            // 違反ユーザーの情報取得
            $member = Member::find($member_id);
            return view("member.violation.create", [
                "request" => $request,
                "member" => $member,
                "violation_list" => $violation_list,
            ]);
        } catch (\Throwable $e) {
            logger()->error($e->getMessage());
            return view("member.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }


    /**
     * 指定したユーザーに対する通報処理入力画面
     *
     * @param BaseViolationRequest $request
     * @param integer $member_id
     * @return void
     */
    public function postCreate(BaseViolationRequest $request, int $member_id)
    {
        try {
            $post_data = $request->validated();
            logger()->info(__FILE__, $post_data);

            // 違反テーブルの親レコードを作成
            $violation_report = ViolationReport::create([
                "from_member_id" => $post_data["from_member_id"],
                "to_member_id" => $post_data["to_member_id"],
                "message" => $post_data["message"],
            ]);

            // nullチェック
            if ($violation_report === NULL) {
                throw new \Exception("違反ユーザーの違反メッセージの報告処理に失敗しました｡");
            }

            $insert_data = [];
            $current = (new \DateTime())->format("Y-m-j H:i:s");
            foreach ($post_data["category_id"] as $key => $value) {
                $insert_data[] = [
                    "category_id" => $value,
                    "violation_report_id" => $violation_report->id,
                    "created_at" => $current,
                    "updated_at" => $current,
                ];
            }
            // 複数レコードを挿入
            $violation_categories = ViolationCategory::insert($insert_data);

            // 戻り値検証
            if ($violation_categories !== true) {
                throw new \Exception("違反ユーザーの違反カテゴリーの報告処理に失敗しました｡");
            }

            // 違反報告したユーザーをブロックする
            $decline = CommonDecline::block($request->from_member_id, $request->to_member_id);
            return redirect()->action("Member\\ViolationController@completed", [
                "member_id" => $member_id,
            ]);
        } catch (\Throwable $e) {
            logger()->error($e->getMessage());
            return view("member.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }


    /**
     * 指定したユーザーに対する通報処理入力画面
     *
     * @param BaseViolationRequest $request
     * @param integer $member_id
     * @return void
     */
    public function completed(BaseViolationRequest $request, int $member_id)
    {
        try {
            // 違反ユーザーの情報取得
            $member = Member::find($member_id);
            return view("member.violation.completed", [
                "request" => $request,
                "member" => $member,
            ]);
        } catch (\Throwable $e) {
            logger()->error($e->getMessage());
            return view("member.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }

}
