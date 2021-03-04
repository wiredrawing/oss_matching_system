@include("member.common.header")
<section>
    <div class="box">
        {{Form::open([
            "url" => action("Member\\SearchController@list"),
            "method" => "GET"
        ])}}

            <div class="selectors">
                <p>年齢(下限)</p>
                <div class="cp_ipselect cp_sl04">
                    {{Form::select("from_age", $request->basic["bottom_ages"])}}
                </div>
            </div>
            <!--selectors-->

            <div class="selectors">
                <p>年齢(上限)</p>
                <div class="cp_ipselect cp_sl04">
                    {{Form::select("to_age", $request->basic["top_ages"])}}
                </div>
            </div>
            <!--selectors-->

            <div class="selectors">
                <p>身長(下限)</p>
                <div class="cp_ipselect cp_sl04">
                    {{Form::select("bottom_height", $request->basic["bottom_height"])}}
                </div>
            </div>
            <!--selectors-->

            <div class="selectors">
                <p>身長(上限)</p>
                <div class="cp_ipselect cp_sl04">
                    {{Form::select("top_height", $request->basic["top_height"])}}
                </div>
            </div>
            <!--selectors-->

            <div class="selectors">
                <p>体型</p>
                <div class="cp_ipselect cp_sl04">
                    {{Form::select("body_style", $body_style)}}
                </div>
            </div>
            <!--selectors-->

            <div class="selectors">
                <p>エリア</p>
                <div class="cp_ipselect cp_sl04">
                    {{Form::select("prefecture", $prefecture)}}
                </div>
            </div>
            <!--selectors-->

            <div class="selectors">
                <p>職業</p>
                <div class="cp_ipselect cp_sl04">
                    {{Form::select("job_type", $job_type)}}
                </div>
            </div>
            <!--selectors-->

            <div class="selectors">
                <p>子供の有無</p>
                <div class="cp_ipselect cp_sl04">
                    {{Form::select("children", $children)}}
                </div>
            </div>
            <!--selectors-->

            <div class="selectors">
                <p>休日</p>
                <div class="cp_ipselect cp_sl04">
                    {{Form::select("day_off", $day_off)}}
                </div>
            </div>
            <!--selectors-->

            <div class="selectors">
                <p>お酒</p>
                <div class="cp_ipselect cp_sl04">
                    {{Form::select("alcohol", $alcohol)}}
                </div>
            </div>
            <!--selectors-->

            <div class="selectors">
                <p>タバコ</p>
                <div class="cp_ipselect cp_sl04">
                    {{Form::select("smoking", $smoking)}}
                </div>
            </div>
            <!--selectors-->

            <div class="selectors">
                <p>パートナー</p>
                <div class="cp_ipselect cp_sl04">
                    {{Form::select("partner", $partner)}}
                </div>
            </div>
            <!--selectors-->

            <div class="selectors">
                <p>ペット</p>
                <div class="cp_ipselect cp_sl04">
                    {{Form::select("pet", $pet)}}
                </div>
            </div>
            <!--selectors-->

            <div class="selectors">
                <p>血液型</p>
                <div class="cp_ipselect cp_sl04">
                    {{Form::select("blood_type", $blood_type)}}
                </div>
            </div>
            <!--selectors-->

            @if ($request->member->gender === "F")
            <div class="selectors">
                <p>年収</p>
                <div class="cp_ipselect cp_sl04">
                    {{Form::select("salary", $salary)}}
                </div>
            </div>
            <!--selectors-->
            @endif

            <!--- 任意のキーワード -->
            <div class="selectors">
                <p>ユーザー名</p>
            </div>
            @if ($errors->has('keyword'))
                <p class="input_red">{{ $errors->first('keyword') }}</p>
            @endif
            {{ Form::input('text', 'keyword', old("keyword"), [
                'placeholder' => '任意のユーザー名で検索',
            ]) }}

            <div class="btnbox">
                <a href="" class="btn search_button">検索</a>
            </div>
        {{Form::close()}}
    </div>
    <!--box-->
</section>
<script>
    $(function () {
        $(".search_button").on("click", function (e) {
            e.preventDefault();
            $("form").eq(0).trigger("submit");
        });
    })
</script>
@include("member.common.footer")
