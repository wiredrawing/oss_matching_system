@include("member.common.header")
<a href="" class="backk">戻る</a>
<section>
    <div class="box">
        <h2>違反者通報フォーム</h2>
        <h5>以下入力フォームに{{$member->display_name}}さんの違反内容を入力して下さい｡</h5>

        {{Form::open([
            "url" => action("Member\\ViolationController@postCreate", [
                "member_id" => $member->id,
            ]),
            "method" => "POST",
        ])}}
            <!-- 隠しパラメータ -->
            {{Form::hidden("from_member_id", $request->member->id)}}
            {{Form::hidden("to_member_id", $member->id)}}
            {{Form::hidden("security_token", $request->member->security_token)}}

            @if($errors->has("category_id"))
            <p class="input_red">{{$errors->first("category_id")}}</p>
            @endif
            <div class="selectors mb32">
                @foreach($violation_list as $key => $value)
                {{Form::checkbox("category_id[]", $key, "", [
                    "id" => "category_id_".$key
                ])}}
                <label for="category_id_{{$key}}">
                    {{$value}}
                </label><br>
                @endforeach
            </div>


            @if ($errors->has("message"))
            <p class="input_red">{{$errors->first("message")}}</p>
            @endif
            {{Form::textarea("message", "", [
                "placeholder" => "こちらに対象のユーザーの違反内容を詳細にご入力下さい｡",
            ])}}
            <p class="input_red">※通報するとお相手とメッセージのやり取りは出来なくなり、通報内容は当事務局にて確認の上処置の検討を致します。</p>
            <div class="btnbox">
                <a href="" class="btn violation_button">上記の内容で通報する</a>
            </div>
        {{Form::close()}}
    </div>
    <!--box-->
</section>
<script>
    $(function () {
        $(".violation_button").on("click", function (e) {
            e.preventDefault();
            $("form").trigger("submit");
        });
    })
</script>
@include("member.common.footer")
