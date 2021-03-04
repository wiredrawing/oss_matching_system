@include("member.common.header")
<section>
    <div class="box">
        <h2 class="noto">パスワード再設定手続き</h2>
        <p class="cation">ご登録のメールアドレスへパスワード再発行の<br>リンクをお送りいたします。</p>
        {{Form::open([
            "url" => action("Member\\EmailController@reissue"),
            "method" => "POST",
        ])}}
        <p>メールアドレス</p>
            @if($errors->has("email"))
            <p class="input_red">{{$errors->first("email")}}</p>
            @endif
            {{Form::email("email", old("email"), [
                "placeholder" => "登録済みのメールアドレスを入力して下さい。"
            ])}}
            <div class="btnbox">
                <a class="submit_button btn">パスワードの再設定のメールを送る</a>
            </div>
        {{Form::close()}}
    </div>
    <!--box-->
</section>
<div class="cation2">
    <h5>確認メールが届かない場合</h5>
    <p>
        しばらくたってから再度お試しいただくか、下記をご確認ください。<br><br>
        入力したメールアドレスが間違っていないか<br>
        過去に登録したメールアドレスになっていないか<br>
        迷惑メールフォルダーに入っていないか<br>
        ご利用メールサービスの受信拒否設定で<br>
        「{{Config("const.domain")}}」が拒否設定になっていないか
    </p>
</div>
<!--cation2-->
<script>
    $(function (e) {
        $(".submit_button").eq(0).on("click", function (e) {
            $("form").eq(0).trigger("submit");
        });
    });
</script>
@include("member.common.footer")
