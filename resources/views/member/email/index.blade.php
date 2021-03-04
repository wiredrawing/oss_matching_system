@include("member.common.header")
<section>
    <div class="box">
        <h2>新規会員仮登録</h2>
        <p class="font12">
            アカウント本登録に関してのメールをお送りいたしますので、<br>メールアドレスをご入力ください。
        </p>
        {{Form::open([
            "url" => action("Member\\EmailController@register"),
            "method" => "POST",
        ])}}
            <p>メールアドレス</p>
            @if ($errors->has("email"))
            <p class="input_red">{{$errors->first("email")}}</p>
            @endif
            {{Form::email("email", "", [
                "placeHolder" => Config("env.dummy_address"),
            ])}}
            <div class="btnbox">
                <a id="submit-form" class="btn">登録</a>
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
