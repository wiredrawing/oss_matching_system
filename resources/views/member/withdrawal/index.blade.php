@include("member.common.header")
<section>
    <div class="box">
        <h2 class="noto">退会手続き</h2>

        <p class="cation">下記アンケートのご協力お願いします。(必須)</p>

        {{Form::open([
            "url" => action("Member\\WithdrawalController@postWithdrawal")
        ])}}
            {{Form::hidden("member_id", $request->member->id)}}

            @if($errors->has("withdrawal"))
            <p class="input_red">{{$errors->first("withdrawal")}}</p>
            @endif
            <div class="selectors mb32">
                @foreach($request->basic["withdrawal"] as $key => $value)
                {{Form::checkbox("withdrawal[]", $key, "", [
                    "id" => "withdrawal_".$key
                ])}} <label for="withdrawal_{{$key}}"> {{$value}} </label><br>
                @endforeach
            </div>

            @if ($errors->has("opinion"))
            <p class="input_red">{{$errors->first("opinion")}}</p>
            @endif
            {{Form::textarea("opinion", old("opinion"), [
                "placeholder" => "ご意見を自由にご記載お願いいたします。",
            ])}}

            <div class="btnbox">
                <a href="" class="btn">退会する</a>
            </div>
        {{Form::close()}}
    </div>
    <!--box-->
</section>
<script>
    $(function (e) {
        $(".btn").on("click", function (e) {
            e.preventDefault();
            $("form").trigger("submit");
        });
    })
</script>
@include("member.common.footer")
