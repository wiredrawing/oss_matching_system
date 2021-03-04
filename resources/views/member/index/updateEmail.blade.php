@include("member.common.header")
<section>
    <div class="box">
        <h2 class="noto">メールアドレス変更申請手続きの完了</h2>
        <p class="cation">
            まだメールアドレスの変更手続きは完了していません｡<br>
            新しいメールアドレスに確認用のメールを送信しましたので､確認用メールをご確認下さい｡
        </p>
        <div class="tex_c">
            <p class="font12">
                <a href="{{action("Member\\IndexController@index")}}">TOPへ戻る</a>
            </p>
        </div>
    </div>
    <!--box-->
</section>
@include("member.common.footer")
