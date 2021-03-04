<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Member;
use App\Http\Requests\Api\EmailRequest;
use App\Common\CommonEmail;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{


    /**
     * Emailアドレスの仮登録
     *
     * @return \Illuminate\Http\EmailRequest
     * @return \Illuminate\Http\Response
     * @return void
     */
    public function register(EmailRequest $request)
    {
        try {
            // log
            logger()->info(__FILE__, $request->validated());

            $member = CommonEmail::register($request);

            if ($member === NULL) {
                logger()->error("入力されたメールアドレスの仮登録に失敗しました。");
                throw new \Exception (Config("errors.CREATE_ERR"));
            }

            $result = [
                "status" => true,
                "response" => [
                    "token" =>$member,
                    "message" => Config("const.email.title.COMPLETED_REGISTERING_EMAIL"),
                ]
            ];

            logger()->info($result);

            // レスポンスはキャッシュさせない
            return response()->json($result);
        } catch (\Exception $e) {
            logger()->error($e);
            $result = [
                "status" => false,
                "response" => [
                    "error" => $e->getMessage()."|".$e->getLine()."|".$e->getFile(),
                ]
            ];
            return response()->json($result);
        }
    }


    /**
     * 本登録時に必要な仮登録済みトークンの検証を行う
     *
     * @param \Illuminate\Http\Request $request
     * @param string $token
     * @return \Illuminate\Http\Response
     */
    public function checkToken(EmailRequest $request, string $token = "")
    {
        try {
            // バリデーション結果を取得
            $get_data = $request->validated();
            logger()->info($get_data);

            // 有効なtoken値から仮登録ユーザーのデータを取得
            $member = Member::where("token", $get_data["token"])
            // 仮登録中
            ->where("is_registered", Config("const.binary_type.off"))
            // 有効期限内のトークンであること
            ->where("expired_at", ">=", (new \DateTime())->format("Y-n-j H:i:s"))
            // 且つ未削除
            ->where("deleted_at", NULL)
            ->get()
            ->first();

            if ($member === NULL) {
                logger()->error($get_data);
                logger()->error($member);
                logger()->error("本登録用トークンが不正です。");
                throw new \Exception(Config("errors.NOT_FOUND_ERR"));
            }

            // Memberオブジェクトをロギング
            logger()->info("member", $member->toArray());

            $result = [
                "status" => true,
                "response" => [
                    "member" => $member,
                ]
            ];
            // レスポンスデータをロギング
            logger()->info($result);
            // レスポンスはキャッシュさせない
            return response()->json($result);
        } catch (\Exception $e) {
            logger()->error($e->getMessage());
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
