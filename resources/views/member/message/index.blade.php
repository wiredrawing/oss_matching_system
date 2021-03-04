@include("member.common.header")
<a href="" class="backk">戻る</a>
<section>
    @if ($matching_users !== NULL && $matching_users->count() > 0)
    <div class="box">
        @foreach ($matching_users as $key => $value)
        <a href="{{action("Member\\MessageController@talk", [ "to_member_id" => $value->member_id ])}}" class="message_list">
            <div class="message_list1">
                @if ((int)$value->image_id > 0)
                <img src="{{action("Api\\v1\\MediaController@show", [
                    "image_id" => $value->image_id,
                    "token" => $value->token
                ])}}">
                @else
                <img src="/images/sample_user.jpg">
                @endif
            </div>
            <div class="message_list2">
                @if (strlen($value->timeline_created_at) > 0)
                <p>{{date("Y年m月d日 H時i分", strtotime($value->timeline_created_at))}}</p>
                @else
                <p>まだ､メッセージは届いていません</p>
                @endif
                <p>
                    @if ($value->income === 1)
                    [VIP]
                    @endif
                    {{$value->display_name}}さんとのメッセージ
                </p>
            </div>
        </a>
        @endforeach
    </div>
    <!--box-->
    @else
    <div class="box">
        <h2 class="noto">メッセージ</h2>
        <p class="cation">現在､メッセージのやり取りが可能なマッチング済みのユーザーはいません｡</p>
        <div class="tex_c">
            <p class="font12">
                <a class="button_to_back_previous_page" href="{{url()->previous()}}">前ページへ戻る</a>
            </p>
        </div>
    </div>
    <!--box-->
    @endif
</section>

<!-- ページング機能 -->
@if ($matching_users->hasPages())
<div class="paging">
    {{-- Previous Page Link --}}
    @if ($matching_users->onFirstPage())
        {{-- <span class="disabled" aria-disabled="true">
            <img src="/images/back.png" width="24">
        </span> --}}
    @else
        <a href="{{ $matching_users->previousPageUrl() }}" rel="prev">
            <img src="/images/back.png" width="24">
        </a>
    @endif
    {{-- Next Page Link --}}
    @if ($matching_users->hasMorePages())
        <a href="{{ $matching_users->nextPageUrl() }}" rel="next">
            <img src="/images/forward.png" width="24">
        </a>
    @else
        {{-- <span class="disabled" aria-disabled="true">
            <img src="/images/forward.png" width="24">
        </span> --}}
    @endif
</div>
@endif
@include("member.common.footer")
