<?php

namespace App\Http\Controllers\Member;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Library\Logger;
use App\Models\Like;
use App\Common\CommonLike;
use App\Http\Requests\BaseLikeRequest;
class LikeController extends Controller
{

    /**
     * 現在、相互マッチ中ユーザー一覧を取得、表示
     *
     * @param Request $request
     * @return void
     */
    public function matching(Request $request)
    {
        try {
            // もらったGood
            $matching_users = CommonLike::getMatchingUsers($request->member->id, $request->excluded_users);
            // log
            Logger::info(__FILE__, $matching_users);

            $prefecture = Config("const.prefecture");
            return view("member.like.match", [
                "request" => $request,
                "matching_users" => $matching_users,
                "prefecture" => $prefecture,
            ]);
        } catch (\Throwable $e) {
            Logger::error(__FILE__, $e);
            return view("member.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }

    /**
     * ログイン中ユーザーがもらったGood
     *
     * @param Request $request
     * @return void
     */
    public function sendLike(Request $request)
    {
        try {
            // 贈ったGood
            $sending_likes = CommonLike::sendingLike($request->member->id, $request->excluded_users);

            // log
            //Logger::info(__FILE__, $sending_likes);
            logger()->info(__FILE__, $sending_likes->toArray());
            //var_dump($request->number_of_sending_likes);
            $prefecture = Config("const.prefecture");
            return view("member.like.send", [
                "request" => $request,
                "sending_likes" => $sending_likes,
                "prefecture" => $prefecture,
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
     * ログイン中ユーザーが贈ったGood
     *
     * @param Request $request
     * @return void
     */
    public function getLike(Request $request)
    {
        try {
            // もらったGood
            $getting_likes = CommonLike::gettingLike($request->member->id, $request->excluded_users);

            // log
            // logger()->info($getting_likes);
            logger()->info(__FILE__, $getting_likes->toArray());

            $prefecture = Config("const.prefecture");
            return view("member.like.get", [
                "request" => $request,
                "getting_likes" => $getting_likes,
                "prefecture" => $prefecture,
            ]);
        } catch (\Throwable $e) {
            Logger::error(__FILE__, $e);
            return view("member.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }

    /**
     * Goodを送る
     *
     * @param BaseLikeRequest $request
     * @return void
     */
    public function create(BaseLikeRequest $request)
    {
        try {
            // Good送信処理
            $like = CommonLike::sendLike($request);

            // LikeオブジェクトNULLチェック
            if ($like === NULL) {
                throw new \Exception(Config("errors.CREATE_LIKE_ERR"));
            }

            return redirect(url()->previous());
        } catch (\Throwable $e) {
            logger()->error($e);
            return view("member.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }
}
