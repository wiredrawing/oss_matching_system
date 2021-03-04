@include("member.common.header")

<section>
    <div class="box">
        <h2 class="noto">まだ､利用条件を満たしておりません｡</h2>

        @if (isset($request->is_identified) && $request->is_identified === false)
        <p><a class="btn_mini_edit" href="{{action("Member\\IndexController@identity")}}">本人確認が完了していません｡</a></p>
        @endif

        @if (isset($request->is_contracted) && $request->is_contracted === false)
        <p><a class="btn_mini_edit" href="{{action("Member\\SubscribeController@index")}}">有料プランの契約が未完了か､期限が切れています｡</a></p>
        @endif
        <div class="tex_c">
            <p class="font12">
                <a class="button_to_back_previous_page" href="{{url()->previous()}}">前ページへ戻る</a>
            </p>
        </div>
    </div>
    <!--box-->
</section>
@include("member.common.footer")
