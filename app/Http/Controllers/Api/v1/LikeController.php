<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LikeRequest;
use App\Models\Like;
use App\Common\CommonLike;

class LikeController extends Controller
{

    /**
     * 指定したターゲットIDに対してGoodを送る
     *
     * @param LikeRequest $request
     * @return void
     */
    public function create(LikeRequest $request)
    {
        try {
            $like = CommonLike::sendLike($request);

            if ($like === NULL) {
                logger()->error("ID[{$request->from_member_id}]からID[{$request->to_member_id}]へのGoodが失敗しました。");
                throw new \Exception(Config("errors.CREATE_ERR"));
            }

            // レスポンスの戻り値
            $result = [
                "status" => true,
                "response" => [
                    "like" => $like,
                ]
            ];

            // log
            logger()->info($result);

            return response()->json($result);
        } catch (\Throwable $e) {
            $result = [
                "status" => false,
                "response" => [
                    "error" => $e->getFile().$e->getLine().$e->getMessage(),
                ]
            ];
            // log
            logger()->error($e);

            return response()->json($result);
        }
    }


    /**
     * URLパラメータにわたされたmember_idがもらったGoodを取得する
     *
     * @param LikeRequest $request
     * @param string $member_id
     * @return Response
     */
    public function getTo(LikeRequest $request, int $member_id)
    {
        try {
            $post_data = $request->validated();
            logger()->info($post_data);

            // もらったGood一覧を取得する
            $like = Like::with([
                "from_member",
            ])->where("to_member_id", $post_data["member_id"])->get();

            // もらったGoodの件数を検証
            if ($like->count() === 0) {
                logger()->error("指定したユーザー[{$post_data["member_id"]}]がもらったGood一覧がありません。");
                throw new \Exception (Config("errors.NOT_FOUND_ERR"));
            }

            $result = [
                "status" => true,
                "response" => [
                    "like" => $like,
                ]
            ];
            logger()->info($result);
            return response()->json($result);
        } catch (\Throwable $e) {
            $result = [
                "status" => false,
                "response" => [
                    "error" => $e->getMessage(),
                ]
            ];
            logger()->error($result);
            return response()->json($result);
        }
    }

    /**
     * URLに指定したmember_idのユーザーがおくったGood一覧を取得
     *
     * @param LikeRequest $request
     * @param integer $member_id
     * @return void
     */
    public function getFrom(LikeRequest $request, int $member_id)
    {
        try {
            $post_data = $request->validated();
            logger()->info($post_data);

            // 贈ったGood一覧を取得する
            $like = Like::with([
                "to_member",
            ])->where("from_member_id", $post_data["member_id"])->get();

            // 贈ったGood一覧を取得する
            if ($like->count() === 0) {
                logger()->error("指定したユーザー[{$post_data["member_id"]}]が贈ったGood一覧がありません。");
                throw new \Exception (Config("errors.NOT_FOUND_ERR"));
            }

            $result = [
                "status" => true,
                "response" => [
                    "like" => $like,
                ]
            ];
            logger()->info($result);
            return response()->json($result);
        } catch (\Throwable $e) {
            $result = [
                "status" => false,
                "response" => [
                    "error" => $e->getMessage(),
                ]
            ];
            logger()->error($result);
            return response()->json($result);
        }
    }


    public function matching (LikeRequest $request, int $member_id)
    {
        try {

        } catch (\Throwable $e) {
            $result = [
                "status" => false,
                "response" => [
                    "error" => $e->getMessage(),
                ]
            ];
            logger()->error($result);
            return response()->json($result);
        }
    }
}
