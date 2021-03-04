@include("member.common.header")
<a href="" class="backk">戻る</a>
<section id="app">
    <div class="box">
        <h2>プロフィール編集</h2>


        <carousel>
            <slide v-for="value in uploaded_images" v-bind:key="value.id">
                <p class="trimm"><img v-bind:src="value.image_thumbnail_url"></p>
                <p><a v-on:click="deleteSelectedImage" v-bind:data-delete-image-id="value.id"
                        class="btn">この画像を削除する</a></p>
            </slide>
        </carousel>

        {{ Form::open([
            'url' => action('Member\\IndexController@postEdit'),
            'method' => 'POST',
        ]) }}
        <!-- 隠しパラメータ -->
        {{ Form::hidden('member_id', $request->member->id, ['ref' => 'member_id']) }}
        {{ Form::hidden('gender', $request->member->gender, ['ref' => 'gender']) }}
        {{ Form::hidden('use_type', Config('const.image.use_type.profile'), ['ref' => 'use_type']) }}
        {{ Form::hidden('is_approved', Config('const.image.approve_type.none'), ['ref' => 'is_approved']) }}
        {{ Form::hidden('security_token', $request->member->security_token, ['ref' => 'security_token']) }}
        <div class="selectors mb32">


            <!-- 画像のぼかしレベルを設定 -->
            <div class="selectors">
                <p>画像のぼかしレベル</p>
                <p class="input_red">最初にどれくらい画像をぼかしたいか設定してください。</p>
                </p>
                <div class="cp_ipselect cp_sl04">
                    {{ Form::select('blur_level', $request->basic['blur_level'], [
                        'ref' => 'blur_level',
                    ]) }}
                </div>
            </div>
            <!-- 画像のぼかしレベルを設定 -->







            <input v-on:change="copmletedSelectingUploadImage" type="file" ref="profile_image"
                class="profile_image_upload" accept="image/jpeg,image/jpg,image/png">

                <div class="tenpu">
                <img ref="profile_image_src" src="">
            </div>

            <input type="hidden" name="delete_image_id">
            <p class="input_red">下記､アップロードボタンをクリックするとすぐに異性に公開されます｡</p>
            <p class="input_red">十分注意して､画像を選択して下さい｡</p>
            <p class="input_red">※アップロードした画像は削除可能です。</p>
            <div class="selectors">
                <a v-on:click="uploadImage" href="" class="btn">この画像をアップロードする</a>
            </div>
        </div>

        <!--- ユーザー名 -->
        <p>ユーザー名</p>
        @if ($errors->has('display_name'))
            <p class="input_red">{{ $errors->first('display_name') }}</p>
        @endif
        {{ Form::input('text', 'display_name', $request->member->display_name, [
            'placeholder' => 'ユーザー名',
        ]) }}


        <!-- メールアドレス -->
        <p>メールアドレス</p>
        @if ($errors->has('email'))
            <p class="input_red">{{ $errors->first('email') }}</p>
        @endif
        {{ Form::email('email', $request->member->email, [
            'readonly' => 'readonly',
        ]) }}


        <!-- 年齢の設定 -->
        @if ($errors->has("age"))
        <p class="input_red">{{$errors->first("age")}}</p>
        @endif
        <div class="selectors">
            <p>年齢</p>
            <div class="cp_ipselect cp_sl04">
                {{Form::select("age", $request->basic["age_list"], $request->member->age)}}
            </div>
        </div>
        <!--selectors-->

        {{-- <!-- 年 -->
        @if ($errors->has('year'))
            <p class="input_red">{{ $errors->first('year') }}</p>
        @endif
        {{ Form::input('hidden', 'birthday', $request->member->birthday) }}
        <div class="selectors">
            <p>生年月日(年)</p>
            <div class="cp_ipselect cp_sl04">
                {{ Form::select('year', $request->basic['year'], $request->member->birthday->format('Y'), [
                    'disabled' => 'disabled',
                ]) }}
                {{ Form::hidden('year', $request->member->birthday->format('Y')) }}
            </div>
        </div>
        <!--selectors-->

        <!-- 月 -->
        @if ($errors->has('month'))
            <p class="input_red">{{ $errors->first('month') }}</p>
        @endif
        <div class="selectors">
            <p>生年月日(月)</p>
            <div class="cp_ipselect cp_sl04">
                {{ Form::select('month', $request->basic['month'], $request->member->birthday->format('n'), [
                    'disabled' => 'disabled',
                ]) }}
                {{ Form::hidden('month', $request->member->birthday->format('n')) }}
            </div>
        </div>
        <!--selectors-->

        <!-- 日 -->
        @if ($errors->has('day'))
            <p class="input_red">{{ $errors->first('day') }}</p>
        @endif
        <div class="selectors">
            <p>生年月日(日)</p>
            <div class="cp_ipselect cp_sl04">
                {{ Form::select('day', $request->basic['day'], $request->member->birthday->format('j'), [
                    'disabled' => 'disabled',
                ]) }}
                {{ Form::hidden('day', $request->member->birthday->format('j')) }}
            </div>
        </div>
        <!--selectors--> --}}

        @if ($errors->has('gender'))
            <p class="input_red">{{ $errors->first('gender') }}</p>
        @endif
        <div class="selectors">
            <p>性別</p>
            <div class="cp_ipselect cp_sl04">
                {{ Form::select('gender', $request->basic['gender'], $request->member->gender, [
                    'disabled' => 'disabled',
                ]) }}
            </div>
        </div>
        <!--selectors-->

        @if ($errors->has('prefecture'))
            <p class="input_red">{{ $errors->first('prefecture') }}</p>
        @endif
        <div class="selectors">
            <p>エリア</p>
            <div class="cp_ipselect cp_sl04">
                {{ Form::select('prefecture', $request->basic['prefecture'], $request->member->prefecture) }}
            </div>
        </div>
        <!--selectors-->

        @if ($errors->has('job_type'))
            <p class="input_red">{{ $errors->first('job_type') }}</p>
        @endif
        <div class="selectors">
            <p>職業</p>
            <div class="cp_ipselect cp_sl04">
                {{ Form::select('job_type', $request->basic['job_type'], $request->member->job_type) }}
            </div>
        </div>
        <!--selectors-->


        @if ($errors->has('height'))
            <p class="input_red">{{ $errors->first('height') }}</p>
        @endif
        <div class="selectors">
            <p>身長</p>
            <div class="cp_ipselect cp_sl04">
                {{ Form::select('height', $request->basic['height'], $request->member->height) }}
            </div>
        </div>

        @if ($errors->has('body_style'))
            <p class="input_red">{{ $errors->first('body_style') }}</p>
        @endif
        <div class="selectors">
            <p>体型</p>
            <div class="cp_ipselect cp_sl04">
                {{ Form::select('body_style', $request->basic['body_style'][$request->member->gender], $request->member->body_style) }}
            </div>
        </div>

        @if ($errors->has('children'))
            <p class="input_red">{{ $errors->first('children') }}</p>
        @endif
        <div class="selectors">
            <p>子供の有無</p>
            <div class="cp_ipselect cp_sl04">
                {{ Form::select('children', $request->basic['children'], $request->member->children) }}
            </div>
        </div>
        <!--selectors-->

        @if ($errors->has('day_off'))
            <p class="input_red">{{ $errors->first('day_off') }}</p>
        @endif
        <div class="selectors">
            <p>休日</p>
            <div class="cp_ipselect cp_sl04">
                {{ Form::select('day_off', $request->basic['day_off'], $request->member->day_off) }}
            </div>
        </div>
        <!--selectors-->

        @if ($errors->has('alcohol'))
            <p class="input_red">{{ $errors->first('alcohol') }}</p>
        @endif
        <div class="selectors">
            <p>お酒</p>
            <div class="cp_ipselect cp_sl04">
                {{ Form::select('alcohol', $request->basic['alcohol'], $request->member->alcohol) }}
            </div>
        </div>
        <!--selectors-->

        @if ($errors->has('smoking'))
            <p class="input_red">{{ $errors->first('smoking') }}</p>
        @endif
        <div class="selectors">
            <p>タバコ</p>
            <div class="cp_ipselect cp_sl04">
                {{ Form::select('smoking', $request->basic['smoking'], $request->member->smoking) }}
            </div>
        </div>
        <!--selectors-->

        <!-- パートナー -->
        @if ($errors->has('partner'))
            <p class="input_red">{{ $errors->first('partner') }}</p>
        @endif
        <div class="selectors">
            <p>パートナー</p>
            <div class="cp_ipselect cp_sl04">
                {{ Form::select('partner', $request->basic['partner'], $request->member->partner) }}
            </div>
        </div>
        <!--selectors-->

        <!-- ペット -->
        @if ($errors->has('pet'))
            <p class="input_red">{{ $errors->first('pet') }}</p>
        @endif
        <div class="selectors">
            <p>ペット</p>
            <div class="cp_ipselect cp_sl04">
                {{ Form::select('pet', $request->basic['pet'], $request->member->pet) }}
            </div>
        </div>
        <!--selectors-->

        <!-- 血液型 -->
        @if ($errors->has('blood_type'))
            <p class="input_red">{{ $errors->first('blood_type') }}</p>
        @endif
        <div class="selectors">
            <p>血液型</p>
            <div class="cp_ipselect cp_sl04">
                {{ Form::select('blood_type', $request->basic['blood_type'], $request->member->blood_type) }}
            </div>
        </div>
        <!--selectors-->

        <!-- 年収 -->
        @if ($errors->has('salary'))
            <p class="input_red">{{ $errors->first('salary') }}</p>
        @endif
        <div class="selectors">
            <p>年収</p>
            <div class="cp_ipselect cp_sl04">
                {{ Form::select('salary', $request->basic['salary'], $request->member->salary) }}
            </div>
        </div>
        <!--selectors-->

        @if ($errors->has('message'))
            <p class="input_red">{{ $errors->first('message') }}</p>
        @endif
        {{ Form::textarea('message', $request->member->message, [
            'placeholder' => '自己PR',
        ]) }}


        <div class="selectors mb32">
            <p>通知の設定</p>
            {{ Form::checkbox('notification_good', 1, $request->member->notification_good, [
                'id' => 'good',
            ]) }}<label for="good">Goodを受信時メール</label><br>
            {{ Form::checkbox('notification_message', 1, $request->member->notification_message, [
                'id' => 'message',
            ]) }}<label for="message">メッセージを受信時メール</label>
        </div>
        <!--selectors-->
        <div class="btnbox">
            <a href="" class="btn update_button">編集完了</a>
        </div>
        {{ Form::close() }}
    </div>
    <!--box-->
</section>
<style>
    .VueCarousel-slide {
        font-size: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        height: auto;
        background: #ccc;
        border-right: 1px solid #FFF;
        box-sizing: border-box;
    }

    .VueCarousel-slide {
	    padding: 0 4px;
    }
    .VueCarousel {
    margin-top: 32px;
    width: 100%;
    background: #fff;
    overflow: hidden !important;
}

</style>
<script>
    // 画面表示時パスワードを非表示
    $(function() {

        // vue
        var app = new Vue({
            el: '#app',
            data: {
                message: 'Hello Vue!',
                member_id: null,
                security_token: null,
                use_type: null,
                api_url: null,
                is_approved: null,
                uploaded_images: [],
                blur_level: 0,
            },
            components: {
                'carousel': VueCarousel.Carousel,
                'slide': VueCarousel.Slide
            },
            created: function() {},
            mounted: function() {
                this.getCurrentProfileImage();
            },
            methods: {
                uploadImage: function(event) {
                    alert("ぼかし処理に5秒から10数秒程度かかります｡しばらくお待ち下さい｡");
                    event.preventDefault();
                    console.dir(this.$refs.profile_image);

                    var params = new FormData();
                    params.append('profile_image', this.$refs.profile_image.files[0]);
                    params.append("blur_level", $("select[name=blur_level]").val());
                    params.append("member_id", this.member_id);
                    params.append("is_approved", this.is_approved);
                    params.append("use_type", this.use_type);
                    //alert(this.is_approved);
                    console.dir(params);
                    // var delete_image_id = parseInt($("input[name=delete_image_id]").eq(index).val());
                    // // 削除対象画像が存在する場合
                    // if (delete_image_id > 0) {
                    //     params.append("delete_image_id", delete_image_id);
                    // }
                    this.$refs.profile_image_src.src = "";
                    this.$refs.profile_image.value = "";
                    var self = this;

                    axios.post("/api/v1/media/image", params).then(function(response) {
                        console.dir(response);
                        if (response.data.status === true) {
                            self.$refs.profile_image_src.src = "";
                            self.$refs.profile_image.value = "";
                            self.getCurrentProfileImage();
                            // $(".profile_image_url").eq(index).find("img").eq(0).attr("src", response.data.response.url);
                            // $("input[name=delete_image_id]").eq(index).val(response.data.response.image.id);
                        } else {
                            alert("プロフィール用画像のアップロードに失敗しました。");
                        }
                        // self.val("");
                    }).catch(function(error) {
                        alert("プロフィール用画像のアップロードに失敗しました。");
                        // self.val("");
                        console.dir(error);
                    });
                },
                getCurrentProfileImage: function() {
                    this.member_id = this.$refs.member_id.value;
                    this.security_token = this.$refs.security_token.value;
                    this.gender = this.$refs.gender.value;
                    this.use_type = this.$refs.use_type.value;
                    this.is_approved = this.$refs.is_approved.value;
                    this.api_url = "/api/v1/media/image/profile/" + this.member_id + "/" + this
                        .security_token;
                    var self = this;
                    // 現在、アップロード中のプロフィール画像一覧
                    axios.get(self.api_url).then(function(response) {
                        console.dir("=============================>");
                        console.dir(response);
                        console.dir("<=============================");
                        if (response.data.status === true) {
                            self.uploaded_images = response.data.response;
                            // if (response.data.response.length > 0) {
                            //     $(".profile_image_url").each (function (index) {
                            //         if (index in response.data.response) {
                            //             $(this).find("img").eq(0).attr("src", response.data.response[index].url)
                            //             $("input[name=delete_image_id]").eq(index).val(response.data.response[index].image.id)
                            //         } else {
                            //             $(this).find("img").eq(0).attr("src", "");
                            //             $("input[name=delete_image_id]").eq(index).val("");
                            //         }
                            //     });
                            // } else {
                            //     // 画像が一枚も設定されていない場合
                            //     $(".profile_image_url").each (function (index) {
                            //         $(this).find("img").eq(0).attr("src", "");
                            //         $("input[name=delete_image_id]").eq(index).val("");
                            //     });
                            // }
                        }
                    })
                },
                copmletedSelectingUploadImage: function(event) {
                    console.dir(this.$refs.profile_image);
                    console.dir(event);
                    var file = event.target.files[0];
                    var file_reader = new FileReader();
                    var self = this;
                    file_reader.onload = function() {
                        console.dir(this);
                        console.dir(self.$refs);
                        self.$refs.profile_image_src.src = this.result;
                    }
                    file_reader.readAsDataURL(file);
                },
                // selectImageToUpload: function (event) {
                //     this.$refs.profile_image.click();
                //     // var self = this;
                //     // axios.post("/api/v1/media/image/", params).then(function(response) {
                //     //     if (response.data.status === true) {
                //     //         $(".profile_image_url").eq(index).find("img").eq(0).attr("src", response.data.response.url);
                //     //         $("input[name=delete_image_id]").eq(index).val(response.data.response.image.id);
                //     //     } else {
                //     //         alert("プロフィール用画像のアップロードに失敗しました。");
                //     //     }
                //     //     self.val("");
                //     // }).catch(function(error) {
                //     //     alert("プロフィール用画像のアップロードに失敗しました。");
                //     //     self.val("");
                //     //     console.dir(error);
                //     // });
                // },
                // 指定した画像を削除する
                deleteSelectedImage: function(event) {
                    if (confirm("指定した画像を削除します｡よろしいですか?")) {
                        console.dir(event);
                        //alert(event.target.dataset.deleteImageId);
                        var params = new URLSearchParams();
                        params.append("member_id", this.member_id);
                        params.append("security_token", this.security_token);
                        params.append("image_id", event.target.dataset.deleteImageId);
                        var self = this;
                        axios.post("/api/v1/media/image/delete", params).then(function(response) {
                            if (response.data.status === true) {
                                console.dir(response);
                                self.getCurrentProfileImage();
                            }
                        });
                    } else {
                        alert("削除をキャンセルしました｡");
                    }
                }
            }
        });

        // 固定パラメータ
        var member_id = $("input[name=member_id]").val();
        var security_token = $("input[name=security_token]").val();

        // // 初回画面ロード時
        // getCurrentProfileImage();

        // Aタグを送信ボタンとして扱う
        $(".update_button").on("click", function(e) {
            e.preventDefault();
            // 年月日を生年月日にフォーマットさせる。
            var year = $("input[name=year]").val();
            var month = $("input[name=month]").val();
            var day = $("input[name=day]").val();
            $("input[name=birthday]").val(year + "-" + month + "-" + day);
            $("form").eq(0).trigger("submit");
            $("form").eq(0).trigger("submit");
        })

        // function getCurrentProfileImage() {
        //     // 現在、アップロード中のプロフィール画像一覧
        //     axios.get("/api/v1/media/image/profile/{{ $request->member->id }}/{{ $request->member->security_token }}").then(function(response) {
        //         if (response.data.status === true) {
        //             if (response.data.response.length > 0) {
        //                 $(".profile_image_url").each (function (index) {
        //                     if (index in response.data.response) {
        //                         $(this).find("img").eq(0).attr("src", response.data.response[index].url)
        //                         $("input[name=delete_image_id]").eq(index).val(response.data.response[index].image.id)
        //                     } else {
        //                         $(this).find("img").eq(0).attr("src", "");
        //                         $("input[name=delete_image_id]").eq(index).val("");
        //                     }
        //                 });
        //             } else {
        //                 // 画像が一枚も設定されていない場合
        //                 $(".profile_image_url").each (function (index) {
        //                     $(this).find("img").eq(0).attr("src", "");
        //                     $("input[name=delete_image_id]").eq(index).val("");
        //                 });
        //             }
        //         }
        //     })
        // }

        // $(".profile_image_upload").hide();
        // $(".profile_image_url").each(function (index) {
        //     $(this).on("click", function(e) {
        //         $(".profile_image_upload").eq(index).trigger("click");
        //     })
        // });
        // $(".profile_image_upload").each(function (index) {
        //     // POST処理
        //     $(this).on("change", function(e) {
        //         var params = new FormData();
        //         var use_type = $("input[name=use_type]").val(); // 本人証明用申請書
        //         var is_approved = {{ $is_approved }}; // 証明書のスターテスを認証申請中にする
        //         var blur_level = $("select[name=blur_level]").val(); // 証明書の場合ぼかさない
        //         params.append('profile_image', e.target.files[0]);
        //         params.append("blur_level", blur_level);
        //         params.append("member_id", member_id);
        //         params.append("is_approved", is_approved);
        //         params.append("use_type", use_type);
        //         var delete_image_id = parseInt($("input[name=delete_image_id]").eq(index).val());
        //         // 削除対象画像が存在する場合
        //         if (delete_image_id > 0) {
        //             params.append("delete_image_id", delete_image_id);
        //         }
        //         var self = $(this);
        //         axios.post("/api/v1/media/image/", params).then(function(response) {
        //             if (response.data.status === true) {
        //                 $(".profile_image_url").eq(index).find("img").eq(0).attr("src", response.data.response.url);
        //                 $("input[name=delete_image_id]").eq(index).val(response.data.response.image.id);
        //             } else {
        //                 alert("プロフィール用画像のアップロードに失敗しました。");
        //             }
        //             self.val("");
        //         }).catch(function(error) {
        //             alert("プロフィール用画像のアップロードに失敗しました。");
        //             self.val("");
        //             console.dir(error);
        //         });
        //     });
        // })

        // // 画像の削除イベント
        // $(".delete_button").each(function (index) {
        //     $(this).on("click", function(e) {
        //         e.preventDefault();
        //         var params = new URLSearchParams();
        //         params.append("member_id", member_id);
        //         params.append("security_token", security_token);
        //         params.append("image_id", $("input[name=delete_image_id]").eq(index).val());
        //         axios.post("/api/v1/media/image/delete", params).then(function (response) {
        //             if (response.data.status === true) {
        //                 console.dir(response);
        //                 getCurrentProfileImage();
        //             }
        //         });
        //     });
        // });
    })

</script>
@include("member.common.footer")
