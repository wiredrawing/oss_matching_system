@include("member.common.header")
<div class="box">
    <h2 class="noto">有料プラン新規契約完了</h2>
    <p class="cation">
        {{$request->member->display_name}}様の有料プランの新規契約が完了致しました｡
        有料プラン期間は､{{$request->member->valid_period->format("Y年m月d日 H時i分")}}まで継続してご利用可能です｡
    </p>
    <div class="tex_c">
        <p class="font12">
            <a href="{{action("Member\\IndexController@index")}}">マイページへ</a>
        </p>
    </div>
</div>
<!--box-->
@include("member.common.footer")
