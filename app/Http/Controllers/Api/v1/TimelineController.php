<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\MessageRequest;
use App\Http\Requests\Api\MediaRequest;
use App\Common\CommonTimeline;
use App\Models\Timeline;

class TimelineController extends Controller
{



    /**
     * タイムラインにメッセージを投稿する処理API
     *
     * @param MessageRequest $request
     * @return void
     */
    public function createMessage(MessageRequest $request)
    {
        try {
            $timeline = CommonTimeline::createMessage($request);
            if ($timeline === NULL) {
                logger()->error( "メッセージの投稿に失敗しました。", $request->validated());
                throw new \Exception(Config("errors.CREATE_ERR"));
            }

            // APIのレスポンス
            $result = [
                "status" => true,
                "response" => [
                    "timeline" => $timeline,
                ]
            ];

            // log
            logger()->error($result);

            return response()->json($result);
        } catch (\Throwable $e) {
            // log
            logger()->error($e);
            $result = [
                "status" => false,
                "response" => [
                    "error" => $e,
                ]
            ];
            return response()->json($result);
        }
    }


    /**
     * タイムラインに画像を投稿するAPI処理
     *
     * @param MediaRequest $request
     * @return void
     */
    public function createImage(MediaRequest $request)
    {
        try {
            $timeline = CommonTimeline::createImage($request);
            if ($timeline === NULL) {
                logger()->error("メッセージの投稿に失敗しました。");
                throw new \Exception(Config("errors.CREATE_ERR"));
            }

            // log
            logger()->info($timeline);

            // APIレスポンスの設定
            $result = [
                "status" => true,
                "response" => [
                    "url" => action("Api\\v1\\MediaController@show", [
                        "image_id" => $timeline->image->id,
                        "token" => $timeline->image->token,
                    ]),
                    "timeline" => $timeline,
                ],
            ];

            // log
            logger()->info($result);

            return response()->json($result);
        } catch (\Throwable $e) {
            // log
            logger()->error($e);
            $result = [
                "status" => false,
                "response" => [
                    "error" => $e,
                ]
            ];
            return response()->json($result);
        }
    }

    /**
     * 指定したルームIDのやりとりを取得する
     *
     * @param MessageRequest $request
     * @param string $room_id
     * @return void
     */
    public function getMessage(MessageRequest $request, int $from_member_id, int $to_member_id, int $timeline_id, int $limit, int $separator)
    {
        try {
            // マッチした組合わせで該当するtimeline_id一覧を取得する
            $temp_timeline = Timeline::select("id")->where(function ($query) use ($request) {
                $query
                ->where("from_member_id", $request->from_member_id)
                ->where("to_member_id", $request->to_member_id);
            })
            ->orWhere(function ($query) use ($request) {
                $query
                ->where("from_member_id", $request->to_member_id)
                ->where("to_member_id", $request->from_member_id);
            })
            ->get();
            $timeline_id_list = array_column($temp_timeline->toArray(), "id");


            $timeline = Timeline::with([
                "message",
                "url",
                "image",
            ]);
            if ($separator > 0 ) {
                $timeline = $timeline->where("id", ">", $timeline_id);
            } else {
                $timeline = $timeline->where("id", "<", $timeline_id);
            }
            $timeline = $timeline->whereIn("id", $timeline_id_list);

            if ($limit > 0) {
                $timeline = $timeline->limit($limit);
            }
            $timeline = $timeline->orderBy("id", "desc");
            if ($separator > 0) {
                $timeline = $timeline->get()->sortBy("id")->values();
            } else {
                $timeline = $timeline->get()->sortByDesc("id")->values();
            }

            $uncheck_timelines = Timeline::where("to_member_id", $request->from_member_id)
            ->where("from_member_id", $request->to_member_id)
            ->update([
                "is_browsed" => Config("const.binary_type.on")
            ]);
            logger()->info($uncheck_timelines);

            foreach($timeline as $key => $value) {
                // プライリティユーザーの場合
                $value->browsing_status = "";
                if (isset($request->priority) && $request->priority === true) {
                    if ((int)$value->is_browsed === Config("const.binary_type.on")) {
                        $value->browsing_status = "既読";
                    }
                }
                // if ($value->image !== NULL) {
                //     $value->image->image_url = action("Api\\v1\\MediaController@show", [
                //         "image_id" => $value->image->id,
                //         "token" => $value->image->token
                //     ]);
                // }
            }
            // print_r($timeline->toArray());
            // APIレスポンスの設定
            $result = [
                "status" => true,
                "response" => [
                    "timeline" => $timeline,
                ],
            ];

            // log
            logger()->info($result);

            return response()->json($result);
        } catch (\Throwable $e) {
            // log
            logger()->error($e);
            $result = [
                "status" => false,
                "response" => [
                    "error" => $e,
                ]
            ];
            return response()->json($result);
        }
    }
}
