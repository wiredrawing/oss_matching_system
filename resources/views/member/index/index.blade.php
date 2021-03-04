@include("member.common.header")
<section>
    <div class="mypagebox">
        <div class="face">
            @if ($member->profile_images->count() > 0)
                <img src="{{ action("Api\\v1\\MediaController@show", [
                    'image_id' => $member->profile_images[0]->id,
                    'token' => $member->profile_images[0]->token,
                ]) }}">
            @else
                <img src="/images/sample_user.jpg">
            @endif
        </div>
        <div class="facebox">
            <!-- ID => {{$request->member->id}} -->
            <p class="id_label">{{ $request->member->display_name }}</p>
            <p class="id_label">{{ $request->basic['prefecture'][$request->member->prefecture] }}</p>

            <!-- 契約中プランの表示 -->
            <p class="id_label">
            @if (isset($request->member->price_plan))
            {{$request->member->price_plan->name}}
                @if ($request->member->valid_period !== null)
                    <p class="font12">
                        有効期限<{{ $request->member->valid_period->format('Y年m月j日 H時i分') }}まで>
                    </p>
                @endif
            @endif
            </p>
            @if ($request->member->identity_image !== null && $request->member->identity_image->is_approved === Config('const.image.approve_type.authenticated'))
                <p class="id_label">本人確認済</p>
            @else
                <div class="tex_c">
                    <a href="{{ action('Member\\IndexController@identity') }}" class="btn_mini_edit">
                        本人確認証明書アップロード
                    </a>
                </div>
            @endif
            @if (isset($request->member->income_image) && $request->member->income_image->is_approved === Config("const.image.approve_type.authenticated"))
                <p class="id_label">VIPユーザー</p>
            @endif
          <div class="tex_c">
            <a href="{{action("Member\\IndexController@edit")}}" class="btn_mini_edit">プロフィール編集</a>
          </div>
          <div class="tex_c">
            <a class="btn_mini_edit" href="{{action("Member\\IndexController@opponent", [
              "target_member_id" => $request->member->id
            ])}}">自身のプロフィールを閲覧</a>
          </div>
        </div>

        <div class="numberbox">
            <div class="numberbox1">
                <p>相互マッチ</p>
                <p class="number">
                    <a href="{{ action('Member\\LikeController@matching') }}">
                        {{ $request->number_of_matching_users }}
                    </a>
                </p>
            </div>
            <div class="numberbox2">
                <p>贈ったGood</p>
                <p class="number">
                    <a href="{{ action('Member\\LikeController@sendLike') }}">{{ $request->number_of_sending_likes }}</a>
                </p>
            </div>
            <div class="numberbox1">
                <p>もらったGood</p>
                <p class="number">
                    <a href="{{ action('Member\\LikeController@getLike') }}">{{ $request->number_of_getting_likes }}</a>
                </p>
            </div>
        </div>
    </div>
    <!--box-->
    <div class="oaitelists_wrap">
        @foreach ($recommended_users as $key => $value)
            <div class="oaitelists">
                <a href="{{ action('Member\\IndexController@opponent', ['target_member_id' => $value->id]) }}"
                    class="oaitelists_img">
                    @if ($value->profile_images->count() > 0)
                        <img
                            src="{{ action("Api\\v1\MediaController@show", ['image_id' => $value->profile_images[0]->id, 'token' => $value->profile_images[0]->token]) }}">
                    @else
                        <img src="/images/sample_user.jpg">
                    @endif
                </a>
                <p class="bold">{{ $value->display_name }}({{$value->age}}歳)</p>
                <p>職業: {{$request->basic["job_type"][$value->job_type]}}</p>
                <p>エリア: {{$request->basic["prefecture"][$value->prefecture]}}</p>
                <p>PR: {{$value->message}}</p>
            </div>
            <!--oaitelists-->
        @endforeach
        {{-- <div class="oaitelists">
            <a href="/detail" class="oaitelists_img">
                <img src="/images/oaite.jpg">
            </a>
            <p class="bold">ミチコ</p>
            <p>東京都</p>
            <p>&starf; 150</p>
        </div>
        <!--oaitelists-->
        <div class="oaitelists">
            <a href="/detail" class="oaitelists_img">
                <img src="/images/oaite.jpg">
            </a>
            <p class="bold">ミチコ</p>
            <p>東京都</p>
            <p>&starf; 150</p>
        </div>
        <!--oaitelists-->
        <div class="oaitelists">
            <a href="/detail" class="oaitelists_img">
                <img src="/images/oaite.jpg">
            </a>
            <p class="bold">ミチコ</p>
            <p>東京都</p>
            <p>&starf; 150</p>
        </div>
        <!--oaitelists-->
        <div class="oaitelists">
            <a href="/detail" class="oaitelists_img">
                <img src="/images/oaite.jpg">
            </a>
            <p class="bold">ミチコ</p>
            <p>東京都</p>
            <p>&starf; 150</p>
        </div>
        <!--oaitelists-->
        <div class="oaitelists">
            <a href="/detail" class="oaitelists_img">
                <img src="/images/oaite.jpg">
            </a>
            <p class="bold">ミチコ</p>
            <p>東京都</p>
            <p>&starf; 150</p>
        </div>
        <!--oaitelists-->
        <div class="oaitelists">
            <a href="/detail" class="oaitelists_img">
                <img src="/images/oaite.jpg">
            </a>
            <p class="bold">ミチコ</p>
            <p>東京都</p>
            <p>&starf; 150</p>
        </div>
        <!--oaitelists-->
        <div class="oaitelists">
            <a href="/detail" class="oaitelists_img">
                <img src="/images/oaite.jpg">
            </a>
            <p class="bold">ミチコ</p>
            <p>東京都</p>
            <p>&starf; 150</p>
        </div>
        <!--oaitelists--> --}}
    </div>
    <!--oaitelists_wrap-->
</section>
@include("member.common.footer")
