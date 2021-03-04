<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\Api\MemberRequest;
use App\Models\Member;
use App\Models\EmailReset;
use App\Library\RandomToken;
use App\Common\CommonMember;
use Illuminate\Support\Facades\Mail;

class MemberController extends Controller
{

    /**
     * 仮登録レコードをベースに、新規ユーザー作成
     *
     * @param MemberRequest $request
     * @param string $token
     * @return \Illuminate\Http\Response
     */
    public function create(MemberRequest $request, string $token)
    {
        try {
            $post_data = $request->validated();
            // 下記新規登録の条件を満たす場合のみ、登録処理を実施する
            // 指定された、tokenカラムが一致且つ、有効期限内、且つメールアドレスがマッチ
            $member = Member::where("token", $post_data["token"])
            ->where("email", $post_data["email"])
            ->where("expired_at", ">=", (new \DateTime())->format("Y-n-j H:i:s"))
            ->where("is_registered", config("const.binary_type.off"))
            ->get()
            ->first();

            // 仮登録ユーザーがみつかない場合は、例外を投げる
            if ($member === NULL) {
                logger()->error($post_data);
                logger()->error("仮登録ユーザーが見つかりませんでした。");
                throw new \Exception(Config("errors.NOT_FOUND_ERR"));
            }

            // 仮登録ユーザーの本登録処理を実行
            $post_data["password"] = password_hash($post_data["password"], PASSWORD_DEFAULT);
            $post_data["is_registered"] = Config("const.binary_type.on");
            $res = $member->fill($post_data)->save();

            // 更新処理に失敗した場合
            if ($res !== true) {
                logger()->error($post_data);
                logger()->error("ユーザー情報の新規登録処理に失敗しました。");
                throw new \Exception(Config("errors.UPDATE_ERR"));
            }

            $result = [
                "status" => true,
                "response" => [
                    "member" => $member,
                ]
            ];
            logger()->info($result);
            return response()->json($result);
        } catch (\Exception $e) {
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
     * 指定したユーザーIDのユーザー情報を取得
     *
     * @param MemberRequest $request
     * @param string $member_id
     * @return Response
     */
    public function show(MemberRequest $request, string $member_id)
    {
        try {
            // 指定したユーザーIDを検索
            $member = Member::find($member_id);

            if ($member === NULL) {
                logger()->error($member_id);
                logger()->error("指定したユーザーID[{$member_id}]のユーザー情報の取得に失敗しました。");
                throw new \Exception (Config("errors.NOT_FOUND_ERR"));
            }

            logger()->info($member);
            // レスポンスの内容
            $result = [
                "status" => true,
                "response" => [
                    "member" => $member,
                ]
            ];
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
     * URLパラメータに指定されたIDのユーザー情報を更新
     *
     * @param MemberRequest $request
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function update(MemberRequest $request, int $member_id)
    {
        try {
            $member = CommonMember::update($request, $member_id);

            if ($member === NULL) {
                logger()->error("指定したユーザーが見つかりませんでした。", $request->validated());
                throw new \Exception (Config("errors.NOT_FOUND_USER_ERR"));
            }

            // APIレスポンスを生成
            $result = [
                "status" => true,
                "response" => [
                    "member" => $member
                ]
            ];

            logger()->info("指定したユーザーID[{$member_id}]のユーザー情報のアップデートが完了しました。", $request->validated());
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
     * ログイン認証
     *
     * @param MemberRequest $request
     * @return Response
     */
    public function authenticate(MemberRequest $request)
    {
        try {
            // バリデーション済みのpostデータを取得
            $post_data = $request->validated();

            $member = Member::where("email", $post_data["email"])
            // 本登録完了済み
            ->where("is_registered", Config("const.binary_type.on"))
            ->get()
            ->first();

            // Memberレコードの検証
            if ($member === NULL) {
                logger()->error("指定したメールアドレス[{$post_data["email"]}]のユーザーが存在しません。");
                throw new \Exception(Config("errors.AUTH_ERR"));
            }

            // パスワードハッシュの参照
            if (\password_verify($post_data["password"], $member->password) !== true) {
                logger()->error("パスワードの認証に失敗しました。");
                logger()->error($member);
                throw new \Exception(Config("errors.AUTH_ERR"));
            }

            // パスワードが一致した場合、新規でセキュリティトークンを生成
            $security_token = RandomToken::MakeRandomToken(128, "SECURITY_");
            $update_data = [
                "security_token" => $security_token,
                "last_login" => (new \DateTime())->format("Y-n-j H:i:s"),
            ];

            $result = $member->fill($update_data)->save();

            logger()->info($member);
            return response()->json([
                "status" => true,
                "response" => [
                    "member" => $member,
                ]
            ]);
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
     * ログアウト処理を実行
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(MemberRequest $request)
    {
        try {
            // セッションを全て破棄する
            $request->session()->flush();

            // statusがtrueであればログアウト成功とする
            $result = [
                "status" => true,
                "response" => [
                    "member" => NULL,
                ]
            ];
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
     * 既存ユーザーのメールアドレス変更前に
     * 新規メールアドレスをemail_resetsテーブルに登録する
     *
     * @param MemberRequest $request
     * @param string $member_id
     * @return Response
     */
    public function email(MemberRequest $request, string $member_id)
    {
        try {
            $post_data = $request->validated();
            logger()->info($post_data);


            // 新規メールアドレスが、membersテーブルに存在しないことを確約する
            $temp = Member::where("email", $post_data["email"])->get()->first();
            if ($temp !== NULL) {
                logger()->error("既に、使用済みのメールアドレス[{$post_data["email"]}に変更しようとしています。");
                throw new \Exception(Config("errors.INTERNAL_ERR"));
            }


            $member = Member::find($post_data["member_id"]);

            if ($member === NULL) {
                logger()->error($post_data);
                logger()->error("メールアドレス変更時、ユーザーID[{$post_data["member_id"]}]のユーザー情報が見つかりませんでした 。");
                throw new \Exception(Config("errors.NOT_FOUND_ERR"));
            }

            // 新規メールアドレスと旧メールアドレスが異なること!
            if ($member->email === $post_data["email"]) {
                logger()->error("新アドレス => ".$post_data["email"]);
                logger()->error("旧アドレス => ".$member->email);
                logger()->error("新旧のアドレスに変更がありません。");
                throw new \Exception(Config("errors.NOT_FOUND_ERR"));
            }

            // メールアドレス変更用トークンとその有効期限を追加
            $post_data["token"] = RandomToken::MakeRandomToken(128, "EMAIL_CHANGE_");
            $post_data["expired_at"] = date("Y-n-j H:i:s", time() + 60 * 60);
            $post_data["is_used"] = Config("const.binary_type.off");

            // 過去にメール変更処理が走っている場合は全て削除する
            $member = EmailReset::where("member_id", $post_data["member_id"])->delete();

            // 新規メールアドレスの登録
            $email_reset = EmailReset::create($post_data);

            // 新規メールリセットの作成に失敗した場合
            if ($email_reset === NULL) {
                throw new \Exception(Config("errors.CREATE_ERR"));
            }
            $result = [
                "status" => true,
                "response" => [
                    "email_reset" => $email_reset,
                ]
            ];

            ///////////////////////////////////////////////////////////////
            // 以下メール送信一覧の処理
            // DBへ新規メールアドレスの登録が完了した後メール送信をする
            ///////////////////////////////////////////////////////////////
            Mail::send(["text" => "templates.updating_email"], [
                "token" => $post_data["token"]
            ],
            function ($message) use ($post_data) {
                $result = $message
                    ->to($post_data["email"])
                    ->from(Config("env.mail_from_address"))
                    ->cc(Config("env.mail_cc"))
                    ->bcc(Config("env.mail_bcc"))
                    ->subject("test");
            });
            $failures = Mail::failures();
            if (count($failures) !== 0) {
                throw new \Exception(Config("errors.EMAIL_ERR"));
            }


            logger()->info($email_reset);
            return response()->json($result);
        } catch(\Throwable $e) {
            $result = [
                "status" => false,
                "response" => [
                    "error" => $e->getMessage().$e->getLine().$e->getFile(),
                ]
            ];
            logger()->error($result);
            return response()->json($result);
        }
    }
}
