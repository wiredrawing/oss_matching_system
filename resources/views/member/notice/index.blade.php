@include("member.common.header")
<a href="" class="backk">戻る</a>
<section>
    @if($logs !== NULL && $logs->count() > 0)
    <div class="box">
        @foreach($logs as $key => $value)
            <a href="{{$value->url}}" class="topics_list">
            <p>{{$value->created_at->format("Y年n月d日 H時i分")}}</p>
            <p>{{$value->from_member->display_name}}さんから{{Config("const.action_message")[$value->action_id]}}</p>
        </a>
        @endforeach
    </div>
    <!--box-->
    @endif
</section>
<!-- ページング機能 -->
@if ($logs->hasPages())
<div class="paging">
    {{-- Previous Page Link --}}
    @if ($logs->onFirstPage())
        {{-- <span class="disabled" aria-disabled="true">
            <img src="/images/back.png" width="24">
        </span> --}}
    @else
        <a href="{{ $logs->previousPageUrl() }}" rel="prev">
            <img src="/images/back.png" width="24">
        </a>
    @endif
    {{-- Next Page Link --}}
    @if ($logs->hasMorePages())
        <a href="{{ $logs->nextPageUrl() }}" rel="next">
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
