@include("member.common.header")
<section>
    <div class="box">
        <h2>会員登録</h2>
        {{Form::open([
            "url" => action("Member\\IndexController@postCreate",[
                "token" => $token,
            ]),
            "method" => "POST",
        ])}}
            <!-- 仮登録時のトークン -->
            {{Form::input("hidden", "token", $token)}}

            <!-- メールアドレス -->
            <p>メールアドレス</p>
            @if ($errors->has("email"))
            <p class="input_red">{{$errors->first("email")}}</p>
            @endif
            {{Form::email("email", $email, [
                "readonly" => "readonly",
            ])}}

            <!-- パスワード -->
            <p>パスワード</p>
            @if ($errors->has("password"))
            <p class="input_red">{{$errors->first("password")}}</p>
            @endif
            <p class="input_red">パスワードは英数を含む8文字以上64文字以下で設定してください｡</p>
            {{Form::password("password", [
                "placeholder" => "パスワード"
            ])}}

            <!-- パスワードチェック -->
            <p>確認用パスワード</p>
            @if ($errors->has("password_check"))
            <p class="input_red">{{$errors->first("password_check")}}</p>
            @endif
            <p class="input_red">パスワードは英数を含む8文字以上64文字以下で設定してください｡</p>
            {{Form::password("password_check", [
                "placeholder" => "確認用パスワード"
            ])}}

            <!--- ユーザー名 -->
            <p>ユーザー名</p>
            @if ($errors->has("display_name"))
            <p class="input_red">{{$errors->first("display_name")}}</p>
            @endif
            {{Form::input("text", "display_name", old("display_name"), [
                "class" => "display_name",
                "placeholder" => "ユーザー名"
            ])}}

            <!-- 年齢の設定 -->
            @if ($errors->has("age"))
            <p class="input_red">{{$errors->first("age")}}</p>
            @endif
            <div class="selectors">
                <p>年齢</p>
                <div class="cp_ipselect cp_sl04">
                    {{Form::select("age", $request->basic["age_list"], old("age"))}}
                </div>
            </div>
            <!--selectors-->

            {{-- <!-- 生年月日 -->
            @if ($errors->has("birthday"))
            <p class="input_red">{{$errors->first("birthday")}}</p>
            @endif
            {{Form::input("hidden", "birthday", old("birthday"))}}

            <!-- 年 -->
            @if ($errors->has("year"))
            <p class="input_red">{{$errors->first("year")}}</p>
            @endif
            <div class="selectors">
                <p>生年月日(年)</p>
                <div class="cp_ipselect cp_sl04">
                    {{Form::select("year", $request->basic["year"], old("year"))}}
                </div>
            </div>
            <!--selectors-->

            <!-- 月 -->
            @if ($errors->has("month"))
            <p class="input_red">{{$errors->first("month")}}</p>
            @endif
            <div class="selectors">
                <p>生年月日(月)</p>
                <div class="cp_ipselect cp_sl04">
                    {{Form::select("month", $request->basic["month"], old("month"))}}
                </div>
            </div>
            <!--selectors-->

            <!-- 日 -->
            @if ($errors->has("day"))
            <p class="input_red">{{$errors->first("day")}}</p>
            @endif
            <div class="selectors">
                <p>生年月日(日)</p>
                <div class="cp_ipselect cp_sl04">
                    {{Form::select("day", $request->basic["day"], old("day"))}}
                </div>
            </div>
            <!--selectors--> --}}

            <!-- 性別 -->
            @if ($errors->has("gender"))
            <p class="input_red">{{$errors->first("gender")}}</p>
            @endif
            <div class="selectors">
                <p>性別</p>
                <div class="cp_ipselect cp_sl04">
                    {{Form::select("gender", $request->basic["gender"], old("gender"))}}
                </div>
            </div>
            <!--selectors-->

            <!-- 都道府県 -->
            @if ($errors->has("prefecture"))
            <p class="input_red">{{$errors->first("prefecture")}}</p>
            @endif
            <div class="selectors">
                <p>お住まいの都道府県<span class="input_red">(必須)</span></p>
                <div class="cp_ipselect cp_sl04">
                    {{Form::select("prefecture", $request->basic["prefecture"], old("prefecture"))}}
                </div>
            </div>
            <!--selectors-->

            <!-- 体型 -->
            {{Form::hidden("body_style", Config("const.binary_type.off"))}}
            {{-- @if ($errors->has("body_style"))
            <p class="input_red">{{$errors->first("body_style")}}</p>
            @endif
            <div class="selectors body_style">
                <p>体型</p>
                <div class="cp_ipselect cp_sl04 body_style M">
                    {{Form::select(
                        "body_style",
                        $request->basic["body_style"]["M"],
                        old("body_style")
                    )}}
                </div>
                <div class="cp_ipselect cp_sl04 body_style F">
                    {{Form::select(
                        "body_style",
                        $request->basic["body_style"]["F"],
                        old("body_style")
                    )}}
                </div>
            </div> --}}
            <!--selectors-->

            <!-- 職業 -->
            {{Form::hidden("job_type", Config("const.binary_type.off"))}}
            {{-- @if ($errors->has("job_type"))
            <p class="input_red">{{$errors->first("job_type")}}</p>
            @endif
            <div class="selectors">
                <p>職業</p>
                <div class="cp_ipselect cp_sl04">
                    {{Form::select("job_type", $request->basic["job_type"], old("job_type"))}}
                </div>
            </div> --}}
            <!--selectors-->

            <!-- 身長 -->
            {{Form::hidden("height", Config("const.binary_type.off"))}}
            {{-- @if ($errors->has("height"))
            <p class="input_red">{{$errors->first("height")}}</p>
            @endif
            <div class="selectors">
                <p>身長</p>
                <div class="cp_ipselect cp_sl04">
                    {{Form::select("height", $request->basic["height"], old("height"))}}
                </div>
            </div> --}}
            <!--selectors-->


            <!-- 子供 -->
            {{Form::hidden("children", Config("const.binary_type.off"))}}
            {{-- @if ($errors->has("children"))
            <p class="input_red">{{$errors->first("children")}}</p>
            @endif
            <div class="selectors">
                <p>子供の有無</p>
                <div class="cp_ipselect cp_sl04">
                    {{Form::select("children", $request->basic["children"], old("children"))}}
                </div>
            </div> --}}
            <!--selectors-->

            <!-- 休日 -->
            {{Form::hidden("day_off", Config("const.binary_type.off"))}}
            {{-- @if ($errors->has("day_off"))
            <p class="input_red">{{$errors->first("day_off")}}</p>
            @endif
            <div class="selectors">
                <p>休日</p>
                <div class="cp_ipselect cp_sl04">
                    {{Form::select("day_off", $request->basic["day_off"], old("day_off"))}}
                </div>
            </div> --}}
            <!--selectors-->

            <!-- 飲酒 -->
            {{Form::hidden("alcohol", Config("const.binary_type.off"))}}
            {{-- @if ($errors->has("alcohol"))
            <p class="input_red">{{$errors->first("alcohol")}}</p>
            @endif
            <div class="selectors">
                <p>お酒</p>
                <div class="cp_ipselect cp_sl04">
                    {{Form::select("alcohol", $request->basic["alcohol"], old("alcohol"))}}
                </div>
            </div> --}}
            <!--selectors-->


            <!-- 喫煙 -->
            {{Form::hidden("smoking", Config("const.binary_type.off"))}}
            {{-- @if ($errors->has("smoking"))
            <p class="input_red">{{$errors->first("smoking")}}</p>
            @endif
            <div class="selectors">
                <p>タバコ</p>
                <div class="cp_ipselect cp_sl04">
                    {{Form::select("smoking", $request->basic["smoking"], old("smoking"))}}
                </div>
            </div> --}}
            <!--selectors-->

            <!-- パートナー -->
            {{Form::hidden("partner", Config("const.binary_type.off"))}}
            {{-- @if ($errors->has("partner"))
            <p class="input_red">{{$errors->first("partner")}}</p>
            @endif
            <div class="selectors">
                <p>パートナー</p>
                <div class="cp_ipselect cp_sl04">
                    {{Form::select("partner", $request->basic["partner"], old("partner"))}}
                </div>
            </div> --}}
            <!--selectors-->

            <!-- ペット -->
            {{Form::hidden("pet", Config("const.binary_type.off"))}}
            {{-- @if ($errors->has("pet"))
            <p class="input_red">{{$errors->first("pet")}}</p>
            @endif
            <div class="selectors">
                <p>ペット</p>
                <div class="cp_ipselect cp_sl04">
                    {{Form::select("pet", $request->basic["pet"], old("pet"))}}
                </div>
            </div> --}}
            <!--selectors-->

            <!-- 血液型 -->
            {{Form::hidden("blood_type", Config("const.binary_type.off"))}}
            {{-- @if ($errors->has("blood_type"))
            <p class="input_red">{{$errors->first("blood_type")}}</p>
            @endif
            <div class="selectors">
                <p>血液型</p>
                <div class="cp_ipselect cp_sl04">
                    {{Form::select("blood_type", $request->basic["blood_type"], old("blood_type"))}}
                </div>
            </div>
            <!--selectors--> --}}

            <!-- 年収 -->
            {{Form::hidden("salary", Config("const.binary_type.off"))}}
            {{-- @if ($errors->has("salary"))
            <p class="input_red">{{$errors->first("salary")}}</p>
            @endif
            <div class="selectors">
                <p>年収</p>
                <div class="cp_ipselect cp_sl04">
                    {{Form::select("salary", $request->basic["salary"], old("salary"))}}
                </div>
            </div> --}}
            <!--selectors-->

            {{Form::hidden("message", "")}}
            {{-- @if ($errors->has("message"))
            <p class="input_red">{{$errors->first("message")}}</p>
            @endif
            {{Form::textarea("message", old("message"), [
                "placeholder" => "自己PR",
            ])}} --}}


            <div class="selectors mb32">
                <p>通知の設定</p>
                <label>
                    {{Form::checkbox("notification_good", 1, old("notification_good"))}} Goodを受信時メール
                </label><br>
                <label>
                    {{Form::checkbox("notification_message", 1, old("notification_message"))}} メッセージを受信時メール
                </label>
            </div>
            <!--selectors-->


            <div class="selectors mb32">
                @if ($errors->has("agree"))
                <p class="input_red">{{$errors->first("agree")}}</p>
                @endif
                <p>利用規約の同意</p>
                <p>※本サービス利用については以下､利用規約に同意する必要があります｡</p>
                <p><a target="_blank" href="/terms">利用規約</a></p>
                <label>
                    {{Form::checkbox("agree", 1, old("agree"))}} 利用規約に同意する
                </label>
            </div>
            <!--selectors-->

            <div class="btnbox">
                <a class="submit_button btn">登録してはじめる</a>
            </div>
        {{Form::close()}}
    </div>
    <!--box-->
</section>
<script>
    $(function () {
        $(".body_style .M, .body_style .M").hide();
        $("select[name='gender']").on("change", function (e) {
            var gender = {
                "F" : "M",
                "M" : "F",
            };
            $("." + $(this).val()).show();
            $("." + gender[$(this).val()]).hide();
        });
        // ページ読み込み直後にイベントを発火
        $("select[name='gender']").trigger("change");
        // フォームの投稿ボタン
        $(".submit_button").on("click", function (e) {
            // 年月日を生年月日にフォーマットさせる。
            var year = $("select[name=year]").val();
            var month = $("select[name=month]").val();
            var day = $("select[name=day]").val();
            $("input[name=birthday]").val(year + "-" + month + "-" + day);
            $("form").eq(0).trigger("submit");
        })
    });
</script>
@include("member.common.footer")
