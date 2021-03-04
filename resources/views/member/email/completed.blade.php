@include("member.common.header")
<section>
    <div class="box">
        <h2 class="noto">仮登録完了</h2>
        <p class="cation">アカウントの作成はまだ完了しておりません。<br>ご注意ください。</p>
        <p>ご登録のメールアドレス宛に「本登録のURL」をお送りしました。<br>
            メールの本文に記載されている「本登録のURL」をクリックし、<br>
            本登録をお願い致します。</p>
        <div class="tex_c">
            <p class="font12"><a href="{{action("Member\\IndexController@index")}}">TOPへ戻る</a></p>
        </div>
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
@include("member.common.footer")
