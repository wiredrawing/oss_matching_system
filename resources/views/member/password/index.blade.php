@include("member.common.header")
<section>
    <div class="box">
        <h2>パスワード再発行処理</h2>
        <p class="font12">
            以下のフォームにパスワードを新しく入力して下さい。
        </p>
        {{Form::open([
            "url" => action("Member\\PasswordController@postUpdate"),
            "method" => "POST",
        ])}}
            <!-- 初回パスワード -->
            <p>パスワード</p>
            @if ($errors->has("password"))
            <p class="input_red">{{$errors->first("password")}}</p>
            @endif
            <p class="input_red">パスワードは英数を含む8文字以上64文字以下で設定してください｡</p>
            {{Form::password("password", [
                "placeHolder" => "パスワード",
            ])}}

            <!-- 確認用パスワード -->
            <p>確認用パスワード</p>
            @if ($errors->has("password_check"))
            <p class="input_red">{{$errors->first("password_check")}}</p>
            @endif
            <p class="input_red">パスワードは英数を含む8文字以上64文字以下で設定してください｡</p>
            {{Form::password("password_check", [
                "placeHolder" => "確認用パスワード",
            ])}}
            <div class="btnbox">
                <a id="submit-form" class="btn">パスワード更新</a>
            </div>
        {{Form::close()}}
    </div>
    <!--box-->
</section>
<script>
    $(function() {
        $("#submit-form").on("click", function (e) {
            $("form").eq(0).trigger("submit");
        });
    });
</script>
@include("member.common.footer")
