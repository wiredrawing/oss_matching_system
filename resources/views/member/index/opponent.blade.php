@include("member.common.header")

<a href="" class="backk">戻る</a>

<section class="opp_wrap_image">
    <div class="opp_imgs">
        @if (count($profile_urls) > 0)
        @foreach ($profile_urls as $key => $value)
            <div class="opp_img"><img src="{{ $value }}"></div>
        @endforeach
        @else
            <div class="opp_img"><img src="/images/sample_user.jpg"></div>
        @endif
    </div>
    <!--opp_imgs-->
</section>

<section class="opp_wrap">

    @if ($opponent->deleted_at === NULL)
    <div class="opp_status">
        @if ($is_match === true)
            <p>相互マッチ中</p>
        @elseif ($is_liking === true)
            <p>Good済み</p>
        @elseif ($is_liked === true)
            <p>あなたをGoodしています</p>
        @endif
    </div>
    @else
    <p>このユーザーは既に退会済みです</p>
    @endif
    <!--opp_status-->


    <div class="box">
        <div class="facebox">
            <div class="tabbs">
            </div>
            <p>{{ $opponent->display_name }}({{$opponent->age}}歳)</p>
            <p>{{ $request->basic["prefecture"][$opponent->prefecture] }}</p>
            @if (isset($opponent->identity_image) && $opponent->identity_image->is_approved === Config("const.image.approve_type.authenticated"))
            <p class="font12">本人確認済み</p>
            @endif
            @if (isset($opponent->income_image) && $opponent->income_image->is_approved === Config("const.image.approve_type.authenticated"))
            <p>VIPユーザー</p>
            @endif
        </div>
        <div class="opp_numberbox">
            <p>もらったGood</p>
            <p class="number">{{ count($opponent->getting_likes) }}</p>
        </div>
    </div>
    <!--box-->

    <div class="balloon1-top">
        <p>{{ $opponent->message }}</p>
    </div>
    <div class="box">
        <table>
            <tr>
                <th>職業</th>
                <td>{{ $job_type[$opponent->job_type] }}</td>
            </tr>
            <tr>
                <th>身長</th>
                <td>{{ $height[$opponent->height] }}</td>
            </tr>

            <!-- 体型表示の際のインデックスエラーに対処 -->
            @if (array_key_exists($opponent->body_style, $body_style[$opponent->gender]))
            <tr>
                <th>体型</th>
                <td>{{ $body_style[$opponent->gender][$opponent->body_style] }}</td>
            </tr>
            @endif

            <tr>
                <th>血液型</th>
                <td>{{ $blood_type[$opponent->blood_type] }}</td>
            </tr>
            <tr>
                <th>子供の有無</th>
                <td>{{ $children[$opponent->children] }}</td>
            </tr>
            <tr>
                <th>休日</th>
                <td>{{ $day_off[$opponent->day_off] }}</td>
            </tr>
            <tr>
                <th>お酒</th>
                <td>{{ $alcohol[$opponent->alcohol] }}</td>
            </tr>
            <tr>
                <th>タバコ</th>
                <td>{{ $smoking[$opponent->smoking] }}</td>
            </tr>
            <tr>
                <th>ペット</th>
                <td>{{ $pet[$opponent->pet] }}</td>
            </tr>
            <tr>
                <th>パートナー</th>
                <td>{{ $partner[$opponent->partner] }}</td>
            </tr>
            <tr>
                <th>年収</th>
                <td>{{ $salary[$opponent->salary] }}</td>
            </tr>
        </table>
    </div>
    <!--box-->

    <!-- 自身のプロフィール閲覧時は表示させない -->
    @if ($request->member->id !== $opponent->id)
        @if ($opponent->deleted_at === NULL)
        <div class="tex_c mt64">
            {{Form::open([
                "url" => action("Member\\DeclineController@block"),
                "method" => "POST",
                "class" => "decline_form"
            ])}}
            {{ Form::hidden('from_member_id', $request->member->id) }}
            {{ Form::hidden('to_member_id', $opponent->id) }}
            {{Form::close()}}
            <a href="" class="block_button">この人をブロックする</a></br>
            <a href="{{action("Member\\ViolationController@create", ["member_id" => $opponent->id])}}" class="violation_button">この人を通報する</a>
        </div>
        @endif
    @endif

</section>

@if ($opponent->deleted_at === NULL && $request->member->id !== $opponent->id)
    @if ($is_liking !== true)
        <!-- Goodを贈っていない場合のみ -->
        <div class="btn_follow_fixed">
            {{ Form::open([
                'url' => action('Member\\LikeController@create'),
                'method' => 'POST',
                "class" => "send_like_form"
            ]) }}
            {{ Form::hidden('from_member_id', $request->member->id) }}
            {{ Form::hidden('to_member_id', $opponent->id) }}
            {{ Form::close() }}
            <a href="" class="btn_follow sending_like">{{$opponent->display_name}}さんにGoodを贈る</a>
        </div>
    @endif

    @if ($is_match === true)
    <!-- マッチ済みの場合のみ表示させる -->
    <div class="btn_follow_fixed">
        <a href="{{action("Member\\MessageController@talk", ["to_member_id" => $opponent->id])}}" class="btn_follow">
            {{$opponent->display_name}}さんにメッセージを送る
        </a>
    </div>
    @endif
@endif

<script>
    // Good送信ボタン
    $(".sending_like").on("click", function(e) {
        e.preventDefault();
        // postデータを生成
        // var params = new URLSearchParams();
        // params.append("from_member_id", {{$request->member->id}});
        // params.append("to_member_id", {{$opponent->id}});
        // axios.post("/api/v1/like/", params).then(function(response) {
        //     console.dir(response);
        // });
        $(".send_like_form").eq(0).trigger("submit");
    });

    // ブロック送信ボタン
    $(".block_button").on("click", function(e) {
        e.preventDefault();
        $(".decline_form").eq(0).trigger("submit");
    });


    $('.opp_imgs').slick({
        centerMode: true,
        dots: true,
        centerPadding: '64px',
        slidesToShow: 1,
        responsive: [{
                breakpoint: 768,
                settings: {
                    arrows: false,
                    centerMode: true,
                    centerPadding: '64px',
                    slidesToShow: 1
                }
            },
            {
                breakpoint: 480,
                settings: {
                    arrows: false,
                    centerMode: true,
                    centerPadding: '64px',
                    slidesToShow: 1
                }
            }
        ]
    });

</script>
@include("member.common.footer")
