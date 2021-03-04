<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Api\DeclineRequest;
use App\Models\Decline;

class DeclineController extends Controller
{

    /**
     * ログイン中のユーザーが指定したユーザーをブロックする
     *
     * @param Decline $request
     * @return Response
     */
    public function create(DeclineRequest $request)
    {
        try {
            // postデータを取得
            $post_data = $request->validated();
            logger()->info($post_data);

            $insert_data = [
                "to_member_id" => $post_data["to_member_id"],
                "from_member_id" => $post_data["from_member_id"],
            ];

            // リクエストの重複チェック
            $duplication_check = Decline::where($insert_data)->get()->first();
            if ($duplication_check !== NULL) {
                logger()->error("既にブロック中です。");
                throw new \Exception(Config("errors.DUPLICATION_ERR"));
            }

            $declined_user = Decline::create($insert_data);
            $result = [
                "status" => true,
                "response" => [
                    "declined_user" => $declined_user,
                ]
            ];
            return response()->json($result);
        } catch (\Throwable $e) {
            $result = [
                "status" => false,
                "response" => [
                    "error" => $e->getMessage()
                ]
            ];
            return response()->json($result);
        }
    }

    /**
     * 指定した、ログインユーザーがブロックしているユーザー一覧を取得する
     *
     * @param Decline $request
     * @return Response
     */
    public function get(DeclineRequest $request, int $member_id)
    {
        try {
            $member_id = $request->validated()["member_id"];
            $declined_users = Decline::with([
                "to_members",
            ])->where("from_member_id", $member_id)->get();

            $result = [
                "status" => true,
                "response" => [
                    "declined_users" => $declined_users
                ]
            ];
            return response()->json($result);
        } catch (\Exception $e) {
            $result = [
                "status" => false,
                "response" => [
                    "error" => $e->getMessage(),
                ]
            ];
            return response()->json($result);
        }
    }

    /**
     * 指定したユーザーIDのブロックを外す
     *
     * @param DeclineRequest $request
     * @param integer $member_id ログイン中ユーザー
     * @return Response
     */
    public function delete(DeclineRequest $request)
    {
        try {
            $post_data = $request->validated();
            logger()->info($post_data);

            // リクエストされた組み合わせの存在チェック
            $declined_user = Decline::where($post_data)->get()->first();
            if ($declined_user === NULL) {
                logger()->error("リクエストされ他条件で、ブロックはありません。");
                throw new \Exception(Config("errors.NOT_FOUND_ERR"));
            }

            // 指定した組み合わせのブロックを物理削除する
            $deleted_num = Decline::where($post_data)->delete();
            logger()->info("削除した件数 => {$deleted_num}");

            $result = [
                "status" => true,
                "response" => [
                    "deleted_num" => $deleted_num
                ]
            ];
            return response()->json($result);
        } catch (\Exception $e) {
            $result = [
                "status" => false,
                "response" => [
                    "error" => $e->getMessage(),
                ]
            ];
            return response()->json($result);
        }
    }
}
