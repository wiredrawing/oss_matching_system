@include("member.common.header")
<section>
    <div class="box">
        <h2 class="noto">パスワード再設定完了</h2>
        <p class="cation">パスワードの再設定が正しく完了しました。次回ログイン時より新しいパスワードでログインできます。</p>
        <div class="tex_c">
            <p class="font12">
                <a href="{{action("Member\\IndexController@index")}}">TOPへ戻る</a>
            </p>
        </div>
    </div>
    <!--box-->
</section>
@include("member.common.footer")
