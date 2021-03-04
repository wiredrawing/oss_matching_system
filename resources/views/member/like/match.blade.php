@include("member.common.header")
<a href="" class="backk">戻る</a>
<section>

    <div class="opp_status">
        <a href="{{action("Member\\LikeController@matching")}}" class="active">相互マッチ</a>
        <a href="{{action("Member\\LikeController@sendLike")}}">贈ったGood</a>
        <a href="{{action("Member\\LikeController@getLike")}}">もらったGood</a>
    </div>
    <!--opp_status-->

    @if ($matching_users->count() > 0)
    <div class="oaitelists_wrap mt0">
        @if ($matching_users !== NULL)
        @foreach ($matching_users as $key => $value)
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
        <!--oaitelists-->
        @endforeach
        @endif
    </div>
    <!--oaitelists_wrap-->
    @else
    <p>まだ､一度もマッチングしていません｡</p>
    @endif

@include("member.common.footer")
