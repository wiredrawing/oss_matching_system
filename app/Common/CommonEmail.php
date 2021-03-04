<?php

namespace App\Common;

// use Illuminate\Http\Request;
// use Illuminate\Http\Response;
use App\Library\RandomToken;
use App\Library\Logger;
use App\Models\Member;
use App\Http\Requests\BaseEmailRequest;
use Illuminate\Support\Facades\Mail;
use App\Models\PasswordReissue;

class CommonEmail
{


    /**
     * Emailアドレスの仮登録
     *
     * @return \Illuminate\Http\EmailRequest
     * @return \Illuminate\Http\Response
     * @return Object
     */
    public static function register(BaseEmailRequest $request)
    {
        try {
            $post_data = $request->validated();
            // 仮登録用トークンの有効期限
            $expired_at = (new \DateTime())->modify("+1 day")->format("Y-m-j H:i:s");
            // フロントへの戻り値
            $result = NULL;
            // 入力されたemailを検索
            $member = Member::withTrashed()
            ->where("email", $post_data["email"])
            ->get()
            ->first();

            // 入力されたメールアドレスが、システム内で有効な場合
            if ($member !== NULL && $member->is_registered === config("const.binary_type.on") && $member->deleted_at === NULL) {
                // log
                logger()->error(__FILE__, $member->toArray());
                logger()->error("指定されたメールアドレス[{$post_data["email"]}]はシステム内で有効な状態です。");
                throw new \Exception(Config("errors.EXISTS_USER_ERR"));
            }


            // 本登録用トークンの生成
            $token = RandomToken::MakeRandomToken(128, "REGISTER_");
            // CSRF用のセキュリティトークンの生成
            $security_token = RandomToken::MakeRandomToken(128, "SECURITY_");
            // DBへの挿入データを構築
            $insert_data = [
                "email" => $post_data["email"],
                "token" => $token,
                "security_token" => $security_token,
                // "gender" => $post_data["gender"],
                // "birthday" => $post_data["birthday"],
                "is_registered" => Config("const.binary_type.off"),
                "expired_at" => $expired_at,
            ];

            // 完全新規登録の場合
            if ($member === NULL) {
                // 新規作成処理
                $member = Member::create($insert_data);
                // sqlの検証
                if ($member->email !== $post_data["email"] ) {
                    throw new \Exception(Config("errors.CREATE_ERR"));
                }
            } else if($member->deleted_at !== NULL) {
                $response = $member->restore();
                if ($response !== true) {
                    throw new \Exception(Config("errors.UPDATE_ERR"));
                }
                $response = $member->fill($insert_data)->save();
                if ($response !== true) {
                    throw new \Exception(Config("errors.UPDATE_ERR"));
                }
            } else {
                // 仮登録を複数回実行した場合
                $update_data = [
                    "email" => $post_data["email"],
                    // "token" => $token,
                    "security_token" => $security_token,
                    // "gender" => $post_data["gender"],
                    // "birthday" => $post_data["birthday"],
                    "is_registered" => Config("const.binary_type.off"),
                    "expired_at" => $expired_at,
                ];
                $response = $member->fill($update_data)->save();
                if ($response !== true) {
                    throw new \Exception(Config("errors.UPDATE_ERR"));
                }
                // テンプレートへ渡すトークンをDB登録済みのものに置き換える
                $token = $member->token;
            }

            ///////////////////////////////
            // 仮登録完了のメール送信処理を実行
            ///////////////////////////////
            Mail::send(["text" => "templates.registered"], [
                "token" => $token,
            ],
            function ($message) use ($insert_data) {
                $result = $message
                    ->to($insert_data["email"])
                    ->from(Config("env.mail_from_address"))
                    ->cc(Config("env.mail_cc"))
                    ->bcc(Config("env.mail_bcc"))
                    ->subject(Config("const.email.title.COMPLETED_REGISTERING_EMAIL"));
            });
            $failures = Mail::failures();
            if (count($failures) !== 0) {
                throw new \Exception(Config("errors.COMPLETED_REGISTERING_EMAIL_ERR"));
            }

            logger()->info(__FILE__, $member->toArray());

            return $member;
        } catch (\Throwable $e) {
            // log
            logger()->error($e);
            return null;
        }
    }


    /**
     * 本登録時に必要な仮登録済みトークンの検証を行う
     *
     * @param string $token
     * @return void
     */
    public static function checkToken(string $token = "") : ?\App\Models\Member
    {
        try {
            // 有効なtoken値から仮登録ユーザーのデータを取得
            $member = Member::where("token", $token)
            // 仮登録中
            ->where("is_registered", Config("const.binary_type.off"))
            // 有効期限内のトークンであること
            ->where("expired_at", ">=", (new \DateTime())->format("Y-n-j H:i:s"))
            // 且つ未削除
            ->where("deleted_at", NULL)
            ->get()
            ->first();

            if ($member === NULL) {
                logger()->error("本登録用トークンが不正です。");
                throw new \Exception(Config("errors.NOT_FOUND_ERR"));
            }

            // Memberオブジェクトをロギング
            logger()->info("member", $member->toArray());

            // $result = [
            //     "status" => true,
            //     "response" => [
            //         "member" => $member,
            //     ]
            // ];
            // レスポンスデータをロギング
            // logger()->info($result);
            // レスポンスはキャッシュさせない
            // return $result;
            logger()->info($member);
            return $member;
        } catch (\Exception $e) {
            logger()->error($e);
            // $result = [
            //     "status" => false,
            //     "response" => [
            //         "error" => $e->getMessage(),
            //     ]
            // ];
            return null;
        }
    }


    /**
     * パスワード再発行用URLを送信する
     *
     * @param string $email
     * @param string $email_template
     * @return Object
     */
    public static function SendPasswordReissueUrl(string $email, string $email_template = "templates.registered"): Object
    {
        // メールアドレスの存在チェック
        $member = Member::where("email", $email)
        ->where("deleted_at", NULL)
        ->where("is_registered", Config("const.binary_type.on"))
        ->get()
        ->first();

        if ($member === NULL) {
            logger()->error("パスワード再発行用URLレコードの作成に失敗しました。");
            throw new \Exception(Config("errors.NOT_FOUND_USER_ERR"));
        }
        // パスワードリセット用URLの有効期限
        $expired_at = (new \DateTime())->modify("+1 day")->format("Y-m-j H:i:s");

        // パスワードリセット用URLトークン
        $token = RandomToken::MakeRandomToken(128, "PASSWORD_");

        // DBへの登録用配列
        $insert_data = [
            "member_id" => $member->id,
            "token" => $token,
            "expired_at" => $expired_at,
            "is_used" => Config("const.binary_type.off"),
        ];

        logger()->info("パスワード再発行用 insert_data dump => ");
        logger()->info($insert_data);

        // 未使用のパスワード再発行用URLレコードの存在チェック
        $password_reissue = PasswordReissue::where("member_id", $member->id)
        ->where("is_used", Config("const.binary_type.off"))
        ->get()
        ->first();

        logger()->info("未使用のパスワード再発行URLレコードの存在チェック");
        logger()->info($password_reissue);

        if ($password_reissue === NULL) {
            // レコード作成
            $password_reissue = new PasswordReissue();
            $password_reissue->fill($insert_data);
            $result = $password_reissue->save();
            if ($result !== true) {
                logger()->error(Config("errors.RESET_PASSWORD_URL_ERR"));
                throw new \Exception(Config("errors.RESET_PASSWORD_URL_ERR"));
            }
        } else {
            // URLトークンはそのままに、有効期限のみを24時間後に更新
            $update_data = [
                "expired_at" => $expired_at,
            ];
            $password_reissue->fill($update_data);
            $result = $password_reissue->save();
            if ($result !== true) {
                logger()->error(Config("errors.RESET_PASSWORD_URL_ERR"));
                throw new \Exception(Config("errors.RESET_PASSWORD_URL_ERR"));
            }
            // 現在DBに保存中のトークンを取得
            $token = $password_reissue->token;
        }

        /////////////////////////////////////
        // パスワード再発行用URLのメール送信処理を実行
        /////////////////////////////////////
        Mail::send(["text" => $email_template], [
            "token" => $token,
        ],
        function ($message) use ($email) {
            $result = $message
                ->to($email)
                ->from(Config("env.mail_from_address"))
                ->cc(Config("env.mail_cc"))
                ->bcc(Config("env.mail_bcc"))
                ->subject(Config("const.email.title.COMPLETED_REISSUE_PASSWORD"));
        });
        $failures = Mail::failures();

        if (count($failures) !== 0) {
            logger()->error($failures);
            throw new \Exception(Config("errors.COMPLETED_REISSUE_PASSWORD_ERR"));
        }
        return $password_reissue;
    }
}
