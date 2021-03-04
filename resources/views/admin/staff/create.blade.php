@include("admin.common.header")
<section class="wrapp">
    <div class="admin_side">
        @include("admin.common.member_menu")
    </div>
    <!--admin_side-->

    <div class="mypagewrap">
        <div class="mypagebox">
            {{ Form::open([
                'url' => action('Admin\\StaffController@postCreate'),
                'method' => 'POST',
            ]) }}
            {{Form::hidden("is_displayed", Config("const.binary_type.off"))}}
            <h2>運営者アカウント作成</h2>
            <p class="tex_c">{{$request->administrator->display_name}}さんログイン中</p>

            <!-- 更新処理時のエラー内容 -->
            <div class="detail_info">
                @foreach($errors->all() as $key => $value)
                <div class="detail_info_list">
                    <p class="input_red">{{ $value }}</p>
                </div>
                @endforeach
            </div>

            <div class="detail_info">
                <div class="detail_info_list">
                    <h5>スタッフ名</h5>
                    @if ($errors->has('display_name'))
                        <p class="input_red">{{ $errors->first('display_name') }}</p>
                    @endif
                    {{ Form::input('text', 'display_name', old("display_name"), [
                        'placeholder' => 'スタッフ名',
                    ]) }}
                </div>
                <!--detail_info_list-->
                <div class="detail_info_list">
                    <h5>スタッフログイン用メールアドレス</h5>
                    @if ($errors->has('email'))
                        <p class="input_red">{{ $errors->first('email') }}</p>
                    @endif
                    {{ Form::input('text', 'email', old("email"), [
                        'placeholder' => 'sample@gmail.com',
                    ]) }}
                </div>
                <!--detail_info_list-->
            </div>
            <!--detail_info-->

            <div class="detail_info">
                <div class="detail_info_list">
                    <h5>パスワード</h5>
                    @if ($errors->has('password'))
                        <p class="input_red">{{ $errors->first('password') }}</p>
                    @endif
                    {{ Form::input('text', 'password', old("password"), [
                        'placeholder' => 'パスワードはパスワード変更時のみ入力して下さい。',
                        'autocomplete' => 'off',
                    ]) }}
                </div>
                <!--detail_info_list-->
                <div class="detail_info_list">
                    <h5>パスワード確認</h5>
                    @if ($errors->has('password_check'))
                        <p class="input_red">{{ $errors->first('password_check') }}</p>
                    @endif
                    {{ Form::input('text', 'password_check', old("password_check"), [
                        'placeholder' => '確認用パスワードはパスワード変更時のみ入力して下さい。',
                        'autocomplete' => 'off',
                    ]) }}
                </div>
                <!--detail_info_list-->
            </div>
            <!--detail_info-->

            <div class="detail_info">
                @if ($errors->has('memo'))
                    <p class="input_red">{{ $errors->first('memo') }}</p>
                @endif
                <h5>備考（事業者側のみ閲覧可能）</h5>
                {{ Form::textarea('memo', old("memo"), [
                    'placeholder' => '管理者向けメモ',
                ]) }}
            </div>
            <!--detail_info-->
            <div class="btnbox">
                <a href="" class="create_staff_button btn">上記内容で運営者アカウントを作成</a>
            </div>
            {{ Form::close() }}
        </div>
        <!--box-->
    </div>
    <!--mypagewrap-->
</section>
<script>
    $(function (e) {
        $(".create_staff_button").on("click", function (e) {
            e.preventDefault();
            $("form").trigger("submit");
        });
    });
</script>
@include("admin.common.footer")
