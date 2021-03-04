@include ("member.common.header")
<section>
    <div class="box">
        <h2>ログイン</h2>
        {{Form::open([
            "url" => action("Member\\LoginController@authenticate"),
            "method" => "POST"
        ])}}

        <!-- ログイン用Email -->
        <p>メールアドレス</p>
        @if ($errors->has("email"))
        <p class="input_red">{{$errors->first("email")}}</p>
        @endif
        {{ Form::text('email', '', [
            'placeholder' => 'メールアドレス',
        ]) }}

        <!-- ログイン用Password -->
        <p>パスワード</p>
        @if ($errors->has("password"))
        <p class="input_red">{{$errors->first("password")}}</p>
        @endif
        {{ Form::password('password', [
            'placeholder' => 'パスワード',
        ]) }}

        {{-- <label>
            {{ Form::checkbox('autologin', '1', false, ['class' => 'circle']) }}
            次回から自動的にログイン
        </label> --}}

        <div class="btnbox">
            <a href="" id="login-button" class="btn">ログイン</a>
        </div>
        <p class="font12">パスワードを忘れた方は<a href="{{action("Member\\EmailController@reissue")}}">こちら</a></p>
        {{ Form::close() }}
    </div>
    <!--box-->
</section>
<div class="out">
    <p class="font12">
        新規会員登録は<a href="{{action("Member\\EmailController@index")}}">こちら</a>
    </p>
</div>
<script>
    $(function() {
        $("#login-button").eq(0).on("click", function(e) {
            e.preventDefault();
            $("form").eq(0).trigger("submit");
        });
    })

</script>
@include("member.common.footer")
