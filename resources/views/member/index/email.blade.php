@include("member.common.header")
<section>
    <div class="box">
        <h2>メールアドレスの変更</h2>
        <h5>ご利用中のメールアドレスを変更できます</h5>
        <p class="font12">
            以下のフォームに新しいメールアドレスを新しく入力して下さい。<br>
            新しいメールアドレスに確認用のメールを送信します｡
        </p>
        {{Form::open([
            "url" => action("Member\\IndexController@updateEmail"),
            "method" => "POST",
        ])}}
            <p>メールアドレス</p>
            @if ($errors->has("email"))
            <p class="input_red">{{$errors->first("email")}}</p>
            @endif
            {{Form::email("email", old("email"), [
                "placeHolder" => "現在のメールアドレス:{$request->member->email}",
            ])}}
            {{ Form::hidden("member_id", $request->member->id)}}
            <div class="btnbox">
                <a id="submit-form" class="btn">メールアドレスを変更</a>
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
    $(function() {
        $("#submit-form").on("click", function (e) {
            $("form").eq(0).trigger("submit");
        });
    });
</script>
@include("member.common.footer")
