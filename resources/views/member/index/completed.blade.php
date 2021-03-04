@include("member.common.header")
<section>
    <div class="box">
        <h2 class="noto">本登録完了</h2>
        <p class="cation">アカウントの本登録が完了しました。登録したメールアドレスとパスワードを使ってログインして下さい。</p>
        <div class="tex_c">
            <p class="font12"><a href={{action("Member\\LoginController@index")}}>ログイン画面へ</a></p>
        </div>
    </div>
    <!--box-->
</section>
@include("member.common.footer")
