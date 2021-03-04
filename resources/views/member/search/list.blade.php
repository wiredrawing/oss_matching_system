@include("member.common.header")
<a href="" class="backk">戻る</a>
<section>
    <div class="oaitelists_wrap mt0">
        @foreach ($members as $key => $value)
        <div class="oaitelists">
            <a href="{{action("Member\\IndexController@opponent", [ "target_member_id" => $value->id ])}}" class="oaitelists_img">
                @if ($value->profile_image_id > 0)
                <img src="{{action("Api\\v1\\MediaController@show", [ "image_id" => $value->profile_image_id, "token" => $value->profile_image_token])}}">
                @else
                <img src="/images/sample_user.jpg">
                @endif
            </a>
            <p class="bold">{{$value->display_name}}({{$value->age}}歳)</p>
            @if (isset($value->income_image_id) && $value->income_image_id > 0)
            <p>VIP</p>
            @endif
            <p>職業: {{$request->basic["job_type"][$value->job_type]}}</p>
            <p>エリア: {{$request->basic["prefecture"][$value->prefecture]}}</p>
            <p>PR: {{$value->message}}</p>
        </div>
        <!--oaitelists-->
        @endforeach
    </div>
    <!--oaitelists_wrap-->


    @if ($members->hasPages())
    <div class="paging">
        {{-- Previous Page Link --}}
        @if ($members->onFirstPage() !== true)
            <a href="{{ $members->previousPageUrl() }}" rel="prev">
                <img src="/images/back.png" width="24">
            </a>
        @endif

        {{-- Next Page Link --}}
        @if ($members->hasMorePages())
            <a href="{{ $members->nextPageUrl() }}" rel="next">
                <img src="/images/forward.png" width="24">
            </a>
        @endif
    </div>
    @endif
@include("member.common.footer")
