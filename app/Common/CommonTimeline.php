<?php

namespace App\Common;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Models\Member;
use App\Models\Message;
use App\Models\Timeline;
use App\Models\Log;
use App\Http\Requests\BaseMessageRequest;
use App\Http\Requests\BaseMediaRequest;
use App\Common\CommonMedia;

class CommonTimeline
{

    /**
     * 投稿したメッセージ本文をmessagesテーブル->timelinesテーブルに登録する
     *
     * @param BaseMessageRequest $request
     * @return Timeline|null
     */
    public static function createMessage(BaseMessageRequest $request): ?Timeline
    {
        try {
            DB::beginTransaction();
            $input_data = $request->validated();

            // log
            logger()->info($input_data);

            // 投稿者のメンバー情報を取得する
            $member = Member::find($request->from_member_id);
            logger()->info($member);

            // 実際の投稿者が正しいかどうかトークンで検証する
            if ($member->security_token !== $request->security_token) {
                logger()->error("security_tokenとmember_idがマッチしません。");
                throw new \Exception(Config("errors.INTERNAL_ERR"));
            }

            // messagesテーブルにメッセージを挿入
            $insert_data = [
                "member_id" => $request->from_member_id,
                "message" => $request->message,
            ];
            $message = Message::create($insert_data);
            if ($message === NULL) {
                logger()->error("messagesテーブルへのメッセージの挿入に失敗しました。");
                logger()->error($insert_data);
                throw new \Exception(Config("errors.CREATE_ERR"));
            }

            // timelinesテーブルにレコードを追加
            $insert_data = [
                "from_member_id" => $request->from_member_id,
                "to_member_id" => $request->to_member_id,
                "timeline_type" => Config("const.timeline.message"),
                "message_id" => $message->id,
            ];

            $timeline = Timeline::create($insert_data);
            if ($timeline === NULL) {
                logger()->error("timelinesテーブルへのメッセージの挿入に失敗しました。");
                logger()->error($insert_data);
                throw new \Exception(Config("errors.CREATE_ERR"));
            }

            // アクション履歴を残す
            $log = Log::create([
                "from_member_id" => $request->from_member_id,
                "to_member_id" => $request->to_member_id,
                "action_id" => Config("const.action.message"),
            ]);
            // log
            logger()->info($log);

            DB::commit();


            // タイムラインへのメッセージ送信完了後､受信したことをメール送信する
            $from_member = Member::findOrFail($request->from_member_id);
            $to_member = Member::findOrFail($request->to_member_id);
            if ((int)$to_member->notification_message === 1) {
                $notification_email = "templates.got_message";
                // メッセージ送信元ユーザーとのチャットページ
                $url = action("Member\\MessageController@talk", [
                    "to_member_id" => $from_member->id,
                ]);
                Mail::send(["text" => $notification_email], [
                        "from_member" => $from_member,
                        "url" => $url,
                    ],
                    function ($message) use ($to_member) {
                        $result = $message
                            ->to($to_member->email)
                            ->from(Config("env.mail_from_address"))
                            ->cc(Config("env.mail_cc"))
                            ->bcc(Config("env.mail_bcc"))
                            ->subject(Config("const.email.title.GOT_MESSAGE"));
                    }
                );
                $failures = Mail::failures();
                if (count($failures) !== 0) {
                    logger()->error("メッセージ受信時の対象異性へのメッセージ通知に失敗しています｡", [
                        "email" => $to_member -> email,
                    ]);
                    logger()->error($failures);
                    // メール送信失敗に関するエラーは無視する
                    // throw new \Exception(Config("errors.EMAIL_ERR"));
                }
            }

            $last_timeline_id = $timeline->id;
            $timeline = Timeline::with([
                "message",
                "image",
                "url"
            ])
            ->where("id", $last_timeline_id)
            ->get()
            ->first();

            // log
            logger()->info($timeline);

            return $timeline;
        } catch (\Throwable $e) {
            // var_dump($e->getMessage());
            // var_dump($e->getLine());
            DB::rollback();
            // log
            logger()->error($e);
            return null;
        }
    }


    /**
     * 投稿した画像をimagesテーブル->timelinesテーブルに登録する
     *
     * @param BaseMediaRequest $request
     * @return Timeline|null
     */
    public static function createImage(BaseMediaRequest $request) : ?Timeline
    {
        try {
            // オブジェクトのプロパティチェック
            if (property_exists($request, "use_type") !== true) {
                // 本メソッド適用時は、タイムライン用途のみとする
                $request->merge([
                    "use_type" => Config("const.image.use_type.timeline")
                ]);
            }

            // 画像アップロードユーザーをrequestオブジェクトに設定
            $request->merge([
                "member_id" => $request->from_member_id,
            ]);
            // 画像アップロード処理を実行
            $image = CommonMedia::upload($request);

            // ImageオブジェクトのNULLチェック
            if ($image === NULL) {
                throw new \Exception(Config("errors.CREATE_ERR"));
            }

            // imagesテーブルへの挿入が確定後、timelinesテーブルにレコードを追加
            $insert_data = [
                "from_member_id" => $request->from_member_id,
                "to_member_id" => $request->to_member_id,
                "timeline_type" => Config("const.timeline.image"),
                "image_id" => $image->id,
            ];

            // timelinesテーブルへの挿入
            $timeline = Timeline::create($insert_data);
            if ($timeline === NULL) {
                logger()->error("timelinesテーブルへのメッセージの挿入に失敗しました。");
                logger()->error($insert_data);
                throw new \Exception(Config("errors.CREATE_ERR"));
            }
            $last_timeline_id = $timeline->id;

            // アクション履歴を残す
            $log = Log::create([
                "from_member_id" => $request->from_member_id,
                "to_member_id" => $request->to_member_id,
                "action_id" => Config("const.action.message"),
            ]);
            // log
            logger()->info($log);

            $timeline = Timeline::with([
                "image",
                "message",
                "url",
            ])
            ->where("id", $last_timeline_id)
            ->get()
            ->first();

            // log
            logger()->info($timeline);

            // タイムラインへの画像アップロード完了後､受信したことをメール送信する
            $from_member = Member::findOrFail($request->from_member_id);
            $to_member = Member::findOrFail($request->to_member_id);
            if ((int)$to_member->notification_message === 1) {
                $notification_email = "templates.got_message";
                // メッセージ送信元ユーザーとのチャットページ
                $url = action("Member\\MessageController@talk", [
                    "to_member_id" => $from_member->id,
                ]);
                Mail::send(["text" => $notification_email], [
                        "from_member" => $from_member,
                        "url" => $url,
                    ],
                    function ($message) use ($to_member) {
                        $result = $message
                            ->to($to_member->email)
                            ->from(Config("env.mail_from_address"))
                            ->cc(Config("env.mail_cc"))
                            ->bcc(Config("env.mail_bcc"))
                            ->subject(Config("const.email.title.GOT_IMAGE"));
                    }
                );
                $failures = Mail::failures();
                if (count($failures) !== 0) {
                    logger()->error($failures);
                    throw new \Exception(Config("errors.EMAIL_ERR"));
                }
            }

            return $timeline;
        } catch (\Throwable $e) {
            logger()->error($e);
            return null;
        }
    }

    /**
     * 未読のメッセージ一覧を取得する
     *
     * @param integer $member_id
     * @param array $exclude_users (ログインユーザーが拒否していものと拒否されているもの両方を除外する)
     * @return array
     */
    public static function uncheckTimelines(int $member_id, array $exclude_users = []): array
    {
        try {
            $timelines = Timeline::select("id")
            ->where("to_member_id", $member_id)
            ->where("is_browsed", Config("const.binary_type.off"))
            ->whereNotIn("from_member_id", $exclude_users)
            ->whereNotIn("to_member_id", $exclude_users)
            ->get();

            $uncheck_timelines = array_column($timelines->toArray(), "id");

            logger()->info($uncheck_timelines);

            return $uncheck_timelines;
        } catch (\Throwable $e) {
            logger()->error($e);
            return [];
        }
    }
}
