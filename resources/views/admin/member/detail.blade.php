@include("admin.common.header")
<section class="wrapp">

    <div class="admin_side">
        @include("admin.common.member_menu")
    </div>
    <!--admin_side-->

    <div class="mypagewrap">
        <div class="mypagebox">
            {{ Form::open([
                'url' => action('Admin\\MemberController@postDetail', [
                    'member_id' => $member->id,
                ]),
                'method' => 'POST',
            ]) }}
            {{ Form::hidden('member_id', $member->id) }}
            {{ Form::hidden('security_token', $member->security_token) }}
            <p class="tex_c">{{$request->administrator->display_name}}さんログイン中</p>

            <!-- 更新処理時のエラー内容 -->
            <div class="detail_info">
                @foreach($errors->all() as $key => $value)
                <div class="detail_info_list">
                    <p class="input_red">{{ $value }}</p>
                </div>
                @endforeach
            </div>

            <div class="detail_info">
                <div class="detail_info_list">
                    <h5>ユーザー名</h5>
                    <p>{{ $member->display_name }}({{$member->age}}歳)さん<br></p>
                </div>
                <!--detail_info_list-->
                <div class="detail_info_list">
                    <h5>ID</h5>
                    {{ Form::input("text", 'id', $member->id, [
                        "disabled" => "disabled",
                    ]) }}
                </div>
                <!--detail_info_list-->
            </div>
            <!--detail_info-->

            <div class="detail_info">
                <div class="detail_info_list">
                    <h5>契約プラン</h5>
                    <p>
                    <div class="cp_ipselect cp_sl04">
                        {{ Form::select('plan_code', $price_plans, $member->plan_code) }}
                    </div>
                    </p>
                </div>
                <!--detail_info_list-->
                <div class="detail_info_list">
                    <h5>登録年月日</h5>
                    @if (isset($member->created_at))
                    {{ Form::text('created_at', $member->created_at->format('Y-m-j H:i:s'), [
                        'disabled' => 'disabled',
                    ]) }}
                    @endif
                </div>
                <!--detail_info_list-->
            </div>
            <!--detail_info-->

            <div class="detail_info">
                <div class="detail_info_list">
                    <h5>最終ログイン日時</h5>
                    @if (isset($member->last_login))
                    {{ Form::text('last_login', $member->last_login->format('Y-m-j H:i:s'), [
                        'disabled' => 'disabled',
                    ]) }}
                    @endif
                </div>
                <!--detail_info_list-->
                <div class="detail_info_list">
                    <h5>現在の有効期間 <span class="font11">(※決済完了の度に更新されます。)</span></h5>
                    @if (isset($member->valid_period))
                        <input type="text" value="{{ $member->valid_period->format('Y-m-j H:i:s') }}">
                    @endif
                </div>
                <!--detail_info_list-->
            </div>
            <!--detail_info-->

            <div class="detail_info">
                <div class="detail_info_list">
                    <h5>本人確認申請画像</h5>
                    @if ($member->identity_image !== NULL)
                        <p><img src="{{ $member->identity_image->image_url }}"></p>
                    @else
                        <p>申請はありません｡</p>
                    @endif

                    @if ($member->identity_image !== NULL)
                        <h5>本人確認</h5>
                        <div class="cp_ipselect cp_sl04">
                            {{ Form::select('is_approved', $approve_type_name, $member->identity_image->is_approved) }}
                        </div>
                    <!--detail_info_list-->
                    @else
                    {{Form::hidden("is_approved", Config("const.image.approve_type.none"))}}
                    @endif

                </div>
                <!--detail_info_list-->
                <div class="detail_info_list">
                    <h5>所得証明申請画像</h5>
                    @if ($member->income_image !== NULL)
                        <p><img src="{{ $member->income_image->image_url }}"></p>
                    @else
                        <p>申請はありません｡</p>
                    @endif

                    @if ($member->income_image !== NULL)
                        <h5>所得証明申請状態(※1)</h5>
                        <div class="cp_ipselect cp_sl04">
                            {{ Form::select('income_certificate', $approve_type_name, $member->income_image->is_approved) }}
                        </div>
                    @else
                    {{Form::hidden("income_certificate", Config("const.image.approve_type.none"))}}
                    @endif

                </div>
                <!--detail_info_list-->
            </div>
            <!--detail_info-->


            <div class="detail_info">
                <h5>プロフィール画像</h5>
                <div class="detail_info_thumb">
                    @foreach ($member->profile_images as $key => $value)
                        <img src="{{$value->image_url}}">
                    @endforeach
                </div>
            </div>

            <div class="detail_info">
                <div class="detail_info_list">
                    <h5>ユーザー名</h5>
                    @if ($errors->has('display_name'))
                        <p class="input_red">{{ $errors->first('display_name') }}</p>
                    @endif
                    {{ Form::input('text', 'display_name', $member->display_name, [
                        'placeholder' => 'ユーザー名',
                    ]) }}
                </div>
                <!--detail_info_list-->
            </div>
            <!--detail_info-->

            <div class="detail_info">
                <div class="detail_info_list">
                    <h5>メールアドレス</h5>
                    @if ($errors->has('email'))
                        <p class="input_red">{{ $errors->first('email') }}</p>
                    @endif
                    {{ Form::email('email', $member->email, [
                        'readonly' => 'readonly',
                    ]) }}
                </div>
                <!--detail_info_list-->
                <div class="detail_info_list">
                    <h5>退会時メールアドレス</h5>
                    @if ($errors->has('deleted_email'))
                        <p class="input_red">{{ $errors->first('deleted_email') }}</p>
                    @endif
                    {{ Form::email('deleted_email', $member->deleted_email, [
                        'readonly' => 'readonly',
                        "placeholder" => "退会ユーザーのみ表示されます｡",
                    ]) }}
                </div>
                <!--detail_info_list-->
            </div>
            <!--detail_info-->

            <div class="detail_info">
                <div class="detail_info_list">
                    <h5>年齢</h5>
                    @if ($errors->has('age'))
                        <p class="input_red">{{ $errors->first('age') }}</p>
                    @endif
                    <div class="cp_ipselect cp_sl04">
                        {{ Form::select('age', $age_list, $member->age) }}
                    </div>
                </div>
                <!--detail_info_list-->
                <div class="detail_info_list">
                    <h5>性別</h5>
                    @if ($errors->has('gender'))
                        <p class="input_red">{{ $errors->first('alcohol') }}</p>
                    @endif
                    <div class="cp_ipselect cp_sl04">
                        {{ Form::select('gender', $gender, $member->gender) }}
                    </div>
                    <p class="input_red">※本登録後の性別は変更に注意して下さい｡</p>
                    </p>
                </div>
                <!--detail_info_list-->
            </div>
            <!--detail_info-->

            <div class="detail_info">
                <div class="detail_info_list">
                    <h5>エリア</h5>
                    @if ($errors->has('prefecture'))
                        <p class="input_red">{{ $errors->first('prefecture') }}</p>
                    @endif
                    <div class="cp_ipselect cp_sl04">
                        {{ Form::select('prefecture', $prefecture, $member->prefecture) }}
                    </div>
                </div>
                <!--detail_info_list-->
                <div class="detail_info_list">
                    <h5>職業</h5>
                    @if ($errors->has('job_type'))
                        <p class="input_red">{{ $errors->first('job_type') }}</p>
                    @endif
                    <div class="cp_ipselect cp_sl04">
                        {{ Form::select('job_type', $job_type, $member->job_type) }}
                    </div>
                </div>
                <!--detail_info_list-->
            </div>
            <!--detail_info-->

            <div class="detail_info">
                <div class="detail_info_list">
                    <h5>身長</h5>
                    @if ($errors->has('height'))
                        <p class="input_red">{{ $errors->first('height') }}</p>
                    @endif
                    <div class="cp_ipselect cp_sl04">
                        {{ Form::select('height', $height, $member->height) }}
                    </div>
                </div>
                <!--detail_info_list-->
                <div class="detail_info_list">
                    <h5>体型</h5>
                    @if ($errors->has('body_style'))
                        <p class="input_red">{{ $errors->first('body_style') }}</p>
                    @endif
                    <div class="cp_ipselect cp_sl04 body_style M">
                        {{Form::select("body_style", $body_style["M"], $member->body_style)}}
                    </div>
                    <div class="cp_ipselect cp_sl04 body_style F">
                        {{Form::select("body_style", $body_style["F"], $member->body_style)}}
                    </div>
                </div>
                <!--detail_info_list-->
            </div>
            <!--detail_info-->

            <div class="detail_info">
                <div class="detail_info_list">
                    <h5>子供の有無</h5>
                    @if ($errors->has('children'))
                        <p class="input_red">{{ $errors->first('children') }}</p>
                    @endif
                    <div class="cp_ipselect cp_sl04">
                        {{Form::select("children", $children, $member->children)}}
                    </div>
                </div>
                <!--detail_info_list-->
                <div class="detail_info_list">
                    <h5>休日</h5>
                    @if ($errors->has('day_off'))
                        <p class="input_red">{{ $errors->first('alcohol') }}</p>
                    @endif
                    <div class="cp_ipselect cp_sl04">
                        {{ Form::select('day_off', $day_off, $member->day_off) }}
                    </div>
                </div>
                <!--detail_info_list-->
            </div>
            <!--detail_info-->

            <div class="detail_info">
                <div class="detail_info_list">
                    <h5>お酒</h5>
                    @if ($errors->has('alcohol'))
                        <p class="input_red">{{ $errors->first('alcohol') }}</p>
                    @endif
                    <div class="cp_ipselect cp_sl04">
                        {{ Form::select('alcohol', $alcohol, $member->alcohol) }}
                    </div>
                </div>
                <!--detail_info_list-->
                <div class="detail_info_list">
                    <h5>タバコ</h5>
                    @if ($errors->has('smoking'))
                        <p class="input_red">{{ $errors->first('alcohol') }}</p>
                    @endif
                    <div class="cp_ipselect cp_sl04">
                        {{ Form::select('smoking', $smoking, $member->smoking) }}
                    </div>
                </div>
                <!--detail_info_list-->
            </div>
            <!--detail_info-->

            <div class="detail_info">
                <div class="detail_info_list">
                    <h5>パートナー</h5>
                    @if ($errors->has('partner'))
                        <p class="input_red">{{ $errors->first('partner') }}</p>
                    @endif
                    <div class="cp_ipselect cp_sl04">
                        {{Form::select("partner", $partner, $member->partner)}}
                    </div>
                </div>
                <!--detail_info_list-->
                <div class="detail_info_list">
                    <h5>ペット</h5>
                    @if ($errors->has('pet'))
                        <p class="input_red">{{ $errors->first('pet') }}</p>
                    @endif
                    <div class="cp_ipselect cp_sl04">
                        {{Form::select("pet", $pet, $member->pet)}}
                    </div>
                </div>
                <!--detail_info_list-->
            </div>
            <!--detail_info-->

            <div class="detail_info">
                <div class="detail_info_list">
                    <h5>血液型</h5>
                    @if ($errors->has('blood_type'))
                        <p class="input_red">{{ $errors->first('blood_type') }}</p>
                    @endif
                    <div class="cp_ipselect cp_sl04">
                        {{Form::select("blood_type", $blood_type, $member->blood_type)}}
                    </div>
                </div>
                <!--detail_info_list-->
                <div class="detail_info_list">
                    <h5>年収</h5>
                    @if ($errors->has('salary'))
                        <p class="input_red">{{ $errors->first('salary') }}</p>
                    @endif
                    <div class="cp_ipselect cp_sl04">
                        {{Form::select("salary", $salary, $member->salary)}}
                    </div>
                </div>
                <!--detail_info_list-->
            </div>
            <!--detail_info-->

            <div class="detail_info">
                <div class="detail_info_list">
                    <h5>本登録状態</h5>
                    <div class="cp_ipselect cp_sl04">
                        {{ Form::select('is_registered', $registering_status, $member->is_registered, [
                            'disabled' => 'disabled',
                        ]) }}
                    </div>
                </div>
                <!--detail_info_list-->
                <div class="detail_info_list">
                    <h5>ブラックフラグ</h5>
                    <div class="cp_ipselect cp_sl04">
                        {{ Form::select('is_blacklisted', $blacklist, $member->is_blacklisted) }}
                    </div>
                </div>
                <!--detail_info_list-->
            </div>
            <!--detail_info-->

            <div class="detail_info">
                <div class="detail_info_list">
                    <h5>パスワード</h5>
                    @if ($errors->has('password'))
                        <p class="input_red">{{ $errors->first('password') }}</p>
                    @endif
                    {{ Form::input('text', 'password', '', [
                        'placeholder' => 'パスワードはパスワード変更時のみ入力して下さい。',
                        'autocomplete' => 'off',
                    ]) }}
                    <p class="input_red">※変更すると､ユーザーにも影響がでます｡<br>ご注意ください｡</p>
                </div>
                <!--detail_info_list-->
                <div class="detail_info_list">
                    <h5>パスワード確認</h5>
                    @if ($errors->has('password_check'))
                        <p class="input_red">{{ $errors->first('password_check') }}</p>
                    @endif
                    {{ Form::input('text', 'password_check', '', [
                        'placeholder' => '確認用パスワードはパスワード変更時のみ入力して下さい。',
                        'autocomplete' => 'off',
                    ]) }}
                    <p class="input_red">※変更すると､ユーザーにも影響がでます｡<br>ご注意ください｡</p>
                </div>
                <!--detail_info_list-->
            </div>
            <!--detail_info-->

            <div class="detail_info">
                <div class="detail_info_list">
                    <h5>決済ID</h5>
                    @if ($errors->has('credit_id'))
                        <p class="input_red">{{ $errors->first('credit_id') }}</p>
                    @endif
                    {{ Form::input('text', 'credit_id', $member->credit_id, [
                        "placeholder" => "有料プラン契約者のみ表示されます｡",
                    ]) }}
                </div>
                <!--detail_info_list-->
                <div class="detail_info_list">
                    <h5>決済開始日時</h5>
                    {{ Form::text('start_payment_date', $member->start_payment_date, [
                        'disabled' => 'disabled',
                    ]) }}
                    <p class="input_red">※変更できません｡(システムにより自動で入力されます)</p>
                </div>
                <!--detail_info_list-->
            </div>
            <!--detail_info-->


            <div class="detail_info">
                <h5>自己PR</h5>
                @if ($errors->has('message'))
                    <p class="input_red">{{ $errors->first('message') }}</p>
                @endif
                {{ Form::textarea('message', $member->message, [
                    'placeholder' => '自己PR',
                ]) }}
            </div>
            <!--detail_info-->
            <div class="detail_info">
                @if ($errors->has('message'))
                    <p class="input_red">{{ $errors->first('memo') }}</p>
                @endif
                <h5>備考（事業者側のみ閲覧可能）</h5>
                {{ Form::textarea('memo', $member->memo, [
                    'placeholder' => '管理者向けメモ',
                ]) }}
            </div>

            <div class="detail_info">
                <div class="detail_info_list">
                    <h5>Good受信時</h5>
                    @if ($errors->has('year'))
                        <p class="input_red">{{ $errors->first('year') }}</p>
                    @endif
                    <div class="cp_ipselect cp_sl04">
                        {{ Form::select('notification_good', $notification, $member->notification_good) }}
                    </div>
                </div>
                <!--detail_info_list-->
                <div class="detail_info_list">
                    <h5>メッセージ受信時</h5>
                    @if ($errors->has('month'))
                        <p class="input_red">{{ $errors->first('month') }}</p>
                    @endif
                    <div class="cp_ipselect cp_sl04">
                        {{ Form::select('notification_message', $notification, $member->notification_message) }}
                    </div>
                </div>
                <!--detail_info_list-->
            </div>
            <!--detail_info-->

            <!--detail_info-->
            <div class="btnbox">
                @if($member->deleted_at !== NULL)
                <p>削除済みユーザーの情報変更はできません｡</p>
                @else
                <a href="" class="update_button btn">編集完了</a>
                @endif
            </div>
            {{ Form::close() }}
        </div>
        <!--box-->
    </div>
    <!--mypagewrap-->
</section>
<script>
    $(function () {
        $(".update_button").on("click", function (e) {
            e.preventDefault();
            // 年月日を生年月日にフォーマットさせる。
            var year = $("select[name=year]").val();
            var month = $("select[name=month]").val();
            var day = $("select[name=day]").val();
            $("input[name=birthday]").val(year + "-" + month + "-" + day);
            $("form").trigger("submit");
        });

        // 選択した性別によって体型のリストを変える
        $(".body_style .M, .body_style .M").hide();
        $("select[name='gender']").on("change", function (e) {
            var gender = {
                "F" : "M",
                "M" : "F",
            };
            //alert($(this).val());
            $("." + $(this).val()).show();
            $("." + gender[$(this).val()]).hide();
        });
        // ページ読み込み直後にイベントを発火
        $("select[name='gender']").trigger("change");
    })
</script>
@include("admin.common.footer")
