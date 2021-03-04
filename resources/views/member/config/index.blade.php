@include("member.common.header")
<a href="" class="backk">戻る</a>
<section>
    <div class="box">
        <a href="{{action("Member\\IndexController@email")}}" class="topics_list">
            メールアドレスを変更する
        </a>
        <a href="{{action("Member\\PasswordController@index")}}" class="topics_list">
            パスワードを変更する
        </a>
        <a href="{{action("Member\\DeclineController@index")}}" class="topics_list">
            ブロック
        </a>
        <a href="{{action("Member\\WithdrawalController@index")}}" class="topics_list">
            退会する
        </a>
        <a href="/terms" class="topics_list">会員規約</a>
        <a href="/info" class="topics_list">特定商取引法に基づく表記</a>
        <a href="/privacy" class="topics_list">プライバシーポリシー</a>
    </div>
    <!--box-->
</section>
@include("member.common.footer")
