@include("member.common.header")

<section>
    <div class="box">
        <h2 class="noto">お知らせ</h2>
        <p class="cation">
            {{$member->display_name}}さんの違反通報が完了しました｡<br>
            ご報告ありがとうございました｡
        </p>
        <div class="tex_c">
            <p class="font12">
                <a href="{{action("Member\\IndexController@index")}}">マイページへ戻る</a>
            </p>
        </div>
    </div>
    <!--box-->
</section>
@include("member.common.footer")
