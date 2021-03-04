@include("member.common.header")
<a href="" class="backk">戻る</a>
<section>
    <h2 class="noto fontpink">足跡</h2>
    <div class="oaitelists_wrap mt0">
        @if ($footprints !== NULL && $footprints->count() > 0)
        @foreach ($footprints as $key => $value)
        <div class="oaitelists">
            <a href="{{action("Member\\IndexController@opponent", ["target_member_id" => $value->id])}}" class="oaitelists_img">
                @if ($value->profile_images->count() > 0)
                <img src="{{action("Api\\v1\\MediaController@show", [ "image_id" => $value->profile_images[0]->id, "token" => $value->profile_images[0]->token])}}">
                @else
                <img src="/images/sample_user.jpg">
                @endif
            </a>
            <p class="bold">{{$value->display_name}}({{$value->age}}歳)</p>
            <p>職業: {{$request->basic["job_type"][$value->job_type]}}</p>
            <p>エリア: {{$request->basic["prefecture"][$value->prefecture]}}</p>
            <p>PR: {{$value->message}}</p>
            <p class="footdate">
                @if (isset($value->from_footprints[0]->updated_at))
                {{$value->from_footprints[0]->updated_at->format("Y年m月d日 H時i分")}}
                @endif
            </p>
        </div>
        <!--oaitelists-->
        @endforeach
        @endif

        <!-- ページング機能 -->
        @if ($footprints->hasPages())
        <div class="paging">
            {{-- Previous Page Link --}}
            @if ($footprints->onFirstPage() !== true)
                <a href="{{ $footprints->previousPageUrl() }}" rel="prev">
                    <img src="/images/back.png" width="24">
                </a>
            @endif

            {{-- Next Page Link --}}
            @if ($footprints->hasMorePages())
                <a href="{{ $footprints->nextPageUrl() }}" rel="next">
                    <img src="/images/forward.png" width="24">
                </a>
            @endif
        </div>
        @endif
    </div>
    <!--oaitelists_wrap-->
</section>
@include("member.common.footer")
