@include("member.common.header")
<section>
    <div class="box planbox">
        @if (strlen($request->member->credit_id) === 0)
        <h2 class="noto">新規有料プラン契約（クレジット払い）</h2>
        {{Form::open([
            "url" => Config("const.telecom.subscribe_url")
        ])}}
            <!-- 実際にPOSTする用パラメータ -->
            {{Form::hidden("rebill_param_id")}}
            {{Form::hidden("money")}}
            {{Form::hidden("clientip", Config("const.telecom.clientip"))}}
            {{Form::hidden("option", $request->member->id)}}
            {{Form::hidden("usrmail", $request->member->email)}}
            {{Form::hidden("redirect_url", $redirect_url)}}
            {{-- {{Form::hidden("redirect_back_url", $redirect_back_url)}} --}}
            {{Form::hidden("non_duplication_id", "yes")}}
            <group class="inline-radio">
                @foreach ($price_plans as $key => $value)
                <div class="bb">
                    {{Form::radio("plan_code", $value->plan_code, [
                        "class" => "plan_code",
                        "checked" => "checked",
                    ])}}
                    <label>{{$value->name}}
                        <span class="pricee">{{number_format($value->price)}}円/月</span>
                    </label>
                </div>
                <!-- ダミー用パラメータ -->
                {{Form::hidden("_rebill_param_id", $value->plan_code)}}
                {{Form::hidden("_money", $value->price)}}
                @endforeach
            </group>
            <div class="btnbox">
                <a href="" class="btn">選択したプランで契約する</a>
            </div>
        {{Form::close()}}
        <img src="/images/payment.png" width="160" class="mt32">
        <div class="selectors mt32">
            <p>お支払いプランをご選択いただき、
                <br>それぞれの決済ページに遷移しお支払いにお進みください。
                <br>尚、カードご利用明細にはサイト名は記載されません。
            </p>
            <p>※クレジットカード払いはVISA、MasterCard、JCBと大手 3 社ブランド様で対応しております。 又、ご請求金額は円建てとなっております。
                <br>※表示価格は全て税抜き価格となります。 申込日よりプランによって当該の日数を適応させて頂きます以降は継続課金となりご返金は一切行っておりません。
                <br>※プランの変更 (ダウングレード・アップグレード) は次回決済のタイミングで可能となります。 即日希望される方は別途お問い合わせ下さいませ。
                <br>※クレジットカード決済の場合、決済完了後即時契約プランの サービスがご利用いただけます。 決済に関しては以下の外部専用システムを利用します。
                <br>カード情報等セキュアな情報は弊社では保有致しません。
            </p>
        </div>
        @else
        <h2 class="noto">現在の有料プラン契約（クレジット払い）</h2>
        <div class="selectors mt32">
            <p>現在､下記プランを契約中です｡別の有料プランに変更する際は､一度有料プランを解約後､再度有料プランを契約して下さい｡</p>
        </div>
        <group class="inline-radio">
            <div class="bb">
                {{Form::radio("plan_code", $request->member->plan_code, [
                    "class" => "plan_code",
                    "checked" => "checked",
                ])}}
                <label>{{$request->member->price_plan->name}}
                    <span class="pricee">{{number_format($request->member->price_plan->price)}}円/月</span>
                </label>
            </div>
        </group>
        <!-- 現在の有料プラン解約時は以下､ボタンクリックで実行 -->
        <div class="btnbox">
            <a href="" class="btn unsubscribe_btn">現在のプランを解約する</a>
            {{Form::open([
                "url" => action("Member\\SubscribeController@unsubscribe"),
                "class" => "unsubscribe_form",
            ])}}
            {{Form::hidden("member_id", $request->member->id)}}
            {{Form::close()}}
        </div>
        <!-- // -->
        @endif
    </div>
    <!--box-->
</section>
<script>
    var plan_code = $(".bb");
    plan_code.eq(plan_code.length - 1).removeClass();
    plan_code.find("input").each(function(index) {
        $(this).on("change", function (e) {
            $("input[name=money]").eq(0).val(
                $("input[name=_money]").eq(index).val()
            );
            $("input[name=rebill_param_id]").eq(0).val(
                $("input[name=_rebill_param_id]").eq(index).val()
            );
        })
    });
    // html初回読み込み時
    plan_code.find("input").eq(0).trigger("change");
    $(".btn").on("click", function (e) {
        e.preventDefault();
        $("form").trigger("submit");
    });
    // 既存契約プランの解約処理を実行
    $(".unsubscribe_btn").on("click", function (e) {
        e.preventDefault();
        if (confirm("現在の有料プランを解約します｡よろしいですか?")) {
            $(".unsubscribe_form").trigger("submit");
        } else {
            alert("解約処理をキャンセルしました｡");
        }
    });
</script>
@include("member.common.footer")
