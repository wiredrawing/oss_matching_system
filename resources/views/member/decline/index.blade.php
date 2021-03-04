@include("member.common.header")
<a href="" class="backk">戻る</a>
<section>
    <h2 class="noto fontpink">ブロックした人</h2>
    <div class="oaitelists_wrap mt0">
        @if ($declining_users !== NULL && $declining_users->count() > 0)
        @foreach ($declining_users as $key => $value)
        <div class="oaitelists">
            <a href="{{action("Member\\IndexController@opponent", ["target_member_id" => $value->id])}}" class="oaitelists_img">
                @if ($value->profile_images->count() > 0)
                <img src="{{action("Api\\v1\\MediaController@show", [
                    "image_id" => $value->profile_images[0]->id,
                    "token" => $value->profile_images[0]->token
                ])}}">
                @else
                <img src="/images/sample_user.jpg">
                @endif
            </a>
            <p class="bold">{{$value->display_name}}({{$value->age}}歳)</p>
            <p>職業: {{$request->basic["job_type"][$value->job_type]}}</p>
            <p>エリア: {{$request->basic["prefecture"][$value->prefecture]}}</p>
            <p>PR: {{$value->message}}</p>
            <a href="{{action("Member\\DeclineController@unblock", [
                "target_member_id" => $value->id
            ])}}" class="unblock">ブロック解除</a>
            <!-- 当該ユーザーのブロック解除処理 -->
            {{Form::open([
                "url" => action("Member\\DeclineController@unblock"),
                "method" => "POST",
                "class" => "unblock_form"
            ])}}
            {{Form::hidden("from_member_id", $request->member->id)}}
            {{Form::hidden("to_member_id", $value->id)}}
            {{Form::close()}}
        </div>
        <!--oaitelists-->
        @endforeach
        @endif
    </div>
    <!--oaitelists_wrap-->

    @if ($declining_users->hasPages())
    <div class="paging">
        {{-- Previous Page Link --}}
        @if ($declining_users->onFirstPage() !== true)
            <a href="{{ $declining_users->previousPageUrl() }}" rel="prev">
                <img src="/images/back.png" width="24">
            </a>
        @endif

        {{-- Next Page Link --}}
        @if ($declining_users->hasMorePages())
            <a href="{{ $declining_users->nextPageUrl() }}" rel="next">
                <img src="/images/forward.png" width="24">
            </a>
        @endif
    </div>
    @endif

    <script>
        // ブロック解除処理の実行
        $(function (e) {
            $(".unblock").each(function (index) {
                $(this).on("click", function (e) {
                    e.preventDefault();
                    $(".unblock_form").eq(index).trigger("submit");
                });
            });
        })
    </script>
@include("member.common.footer")
