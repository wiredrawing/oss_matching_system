@include("member.common.header")
<a href="" class="backk">戻る</a>
<section>

    <div class="opp_status">
        <a href="{{action("Member\\LikeController@matching")}}">相互マッチ</a>
        <a href="{{action("Member\\LikeController@sendLike")}}">贈ったGood</a>
        <a href="{{action("Member\\LikeController@getLike")}}" class="active">もらったGood</a>
    </div>
    <!--opp_status-->

    @if ($getting_likes->total() > 0)
    <div class="oaitelists_wrap mt0">
        @foreach ($getting_likes as $key => $value)
        <div class="oaitelists">
            @if (count($value->profile_images) > 0)
            <a href="{{action("Member\\IndexController@opponent", [
                "target_member_id" => $value->id,
            ])}}" class="oaitelists_img">
                <img src="{{action("Api\\v1\\MediaController@show", [
                    "image_id" => $value->profile_images[0]->id,
                    "token" => $value->profile_images[0]->token,
                ])}}">
            </a>
            @else
            <a href="{{action("Member\\IndexController@opponent", [
                "target_member_id" => $value->id,
            ])}}" class="oaitelists_img">
                <img src="/images/sample_user.jpg">
            </a>
            @endif
            <p class="bold">{{$value->display_name}}({{$value->age}}歳)</p>
            <p>職業: {{$request->basic["job_type"][$value->job_type]}}</p>
            <p>エリア: {{$request->basic["prefecture"][$value->prefecture]}}</p>
            <p>PR: {{$value->message}}</p>
        </div>
        @endforeach
        <!--oaitelists-->
    </div>
    <!--oaitelists_wrap-->
    @else
    <p>まだ､一度もGoodをもらっていません｡</p>
    @endif

    @if ($getting_likes->hasPages())
    <div class="paging">
        {{-- Previous Page Link --}}
        @if ($getting_likes->onFirstPage() !== true)
            <a href="{{ $getting_likes->previousPageUrl() }}" rel="prev">
                <img src="/images/back.png" width="24">
            </a>
        @endif

        {{-- Next Page Link --}}
        @if ($getting_likes->hasMorePages() === true)
            <a href="{{ $getting_likes->nextPageUrl() }}" rel="next">
                <img src="/images/forward.png" width="24">
            </a>
        @endif
    </div>
    @endif
@include("member.common.footer")
