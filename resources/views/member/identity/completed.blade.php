@include("member.common.header")

<section>
    <div class="box">
        <h2 class="noto">本人確認</h2>
        <p class="cation mb32">本人確認書類のご提出ありがとうございます。
            <br>本人確認が承認されましたらご連絡させていただきます。</p>
        <a href="{{action("Member\\IndexController@index")}}">マイページへ戻る</a>
    </div>
    <!--box-->
</section>

@include("member.common.footer");
