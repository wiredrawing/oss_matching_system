@include("member.common.header")
<section>
    <div class="box">
        <h2 class="noto">パスワード再設定手続き</h2>
        <p class="cation mb32">ご登録のメールアドレスへパスワード再発行の
            <br>リンクをお送りいたしました。
            <br>パスワード再発行URLの有効期限は、<br>{{$password_reissue->expired_at->format("Y年m月d日 H時i分")}}までとなります。
        </p>
        <a href="{{action("Member\\IndexController@index")}}">マイページへ戻る</a>
    </div>
    <!--box-->
</section>
@include("member.common.footer")
