@include("member.common.header")

<section>
    <div class="box">
        <h2 class="noto">収入証明確認</h2>
        <p class="cation mb32">収入証明確認書類のご提出ありがとうございます。
            <br>収入証明の確認後､承認されましたらご連絡させていただきます。</p>
        <a href="{{action("Member\\IndexController@index")}}">マイページへ戻る</a>
    </div>
    <!--box-->
</section>

@include("member.common.footer");
