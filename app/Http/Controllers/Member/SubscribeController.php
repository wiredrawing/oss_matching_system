<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\PricePlan;
use App\Models\Member;
use App\Http\Requests\BaseSubscribeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SubscribeController extends Controller
{



    /**
     * 有料プラン選択画面
     *
     * @param Request $request
     * @return void
     */
    public function index(BaseSubscribeRequest $request)
    {
        // 現在､購読可能の有料プラン一覧を取得する
        $price_plans = PricePlan::where([
            "is_displayed" => Config("const.binary_type.on"),
        ])->get();

        // 決済完了時の本アプリケーションへのリダイレクト用URL
        $redirect_back_url = action("Member\\IndexController@index");
        $redirect_url = action("Member\\SubscribeController@completedSubscribing");

        return view("member.subscribe.index", [
            "request" => $request,
            "price_plans" => $price_plans,
            "redirect_back_url" => $redirect_back_url,
            "redirect_url" => $redirect_url,
        ]);
    }


    /**
     * 現在契約中の有料プランの解約処理を実行する
     *
     * @param Request $request
     * @return void
     */
    public function unsubscribe (BaseSubscribeRequest $request)
    {
        try {
            // 有料プラン解約希望のユーザー情報を取得する
            $member = Member::findOrFail($request->member_id);
            // クレジットサーバーに解約処理リクエストを送信
            $request_data = [
                "clientip" => Config("const.telecom.clientip"),
                "member_id" => $member->credit_id,
                // 本システムではテレコム側のパスワードは使用しない
                "password" => "NA",
                "mode" => "link",
            ];
            $response = Http::asForm()->post(Config("const.telecom.withdrawal_url"), $request_data)->throw();
            logger()->info("クレジットサーバーへの解約処理用リクエストデータ", $request_data);

            // 解約処理の成功チェック
            if ($response->body() !== "OK") {
                logger()->error("クレジットサーバーからの解約処理レスポンスがOKではありませんでした｡", ["body" => $response->body()]);
                logger()->error("有料プラン解約処理に失敗したユーザー => ", $member->toArray());
                throw new \Exception("解約処理に失敗しました｡");
            }

            // 解約処理完了後､解約完了ページへと遷移させる｡
            // これは有料プランの解約処理のみ
            return redirect()->action("Member\\SubscribeController@completedUnsubscribing");
        } catch (\Throwable $e) {
            logger()->error($e->getMessage());
            return view("member.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }

    /**
     * 有料プランの新規契約完了ページ
     *
     * @param Request $request
     * @return void
     */
    public function completedSubscribing(BaseSubscribeRequest $request)
    {
        return view("member.subscribe.completedSubscribing", [
            "request" => $request,
        ]);
    }

    /**
     * 契約済み有料プランの解約処理完了ページ
     *
     * @param Request $request
     * @return void
     */
    public function completedUnsubscribing(BaseSubscribeRequest $request)
    {
        return view("member.subscribe.completedUnsubscribing", [
            "request" => $request,
        ]);
    }
}
