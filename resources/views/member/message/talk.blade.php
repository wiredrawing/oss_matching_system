@include("member.common.header")
<a href="" class="backk">戻る</a>
<div id="chat_area">
    <section>
        <div class="message_face">
            @if ($member->profile_images->count() > 0)
                <a href="{{action("Member\\IndexController@opponent", ["target_member_id" => $member->id])}}">
                    <img src="{{ action("Api\\v1\\MediaController@show", [
                        'image_id' => $member->profile_images[0]->id,
                        'token' => $member->profile_images[0]->token,
                    ]) }}">
                </a>
            @else
            <a href="{{action("Member\\IndexController@opponent", ["target_member_id" => $member->id])}}">
                <img src="/images/sample_user.jpg">
            </a>
            @endif
        </div>
        {{ Form::hidden('security_token', $request->member->security_token, [
            'ref' => 'security_token',
        ]) }}
        {{ Form::hidden('from_member_id', $request->member->id, [
            'ref' => 'from_member_id',
        ]) }}
        {{ Form::hidden('to_member_id', $member->id, [
            'ref' => 'to_member_id',
        ]) }}
        <p>{{ $member->display_name }}</p>

        <div class="talk">
            <p v-on:click="getOldMessage" class="past_message_btn">過去のメッセージを取得</p>
            <div v-for="chat in chat_list" v-bind:key="chat.id" v-if="chat.from_member_id == from_member_id">
                <div class="talkin_r">
	                <p class="kidokuornot">@{{chat.browsing_status}}</p>
                    <div v-if="chat.message != null" class="balloon1-right">
                        <p><!--@{{chat.id}}-->@{{ chat . message . message }}</p>
                    </div>
                    <div v-if="chat.image != null" class="balloon1-right">
                        <p>
                            <a target="_blank" v-bind:href="chat.image.image_url">
                                <img width="100" v-bind:src="chat.image.image_thumbnail_url">
                            </a>
                        </p>
                    </div>

                </div>
            </div>
            <div v-else>
                <div class="talkin_l">
                    <div v-if="chat.message != null" class="balloon1-left">
                        <p><!--@{{chat.id}}-->@{{ chat . message . message }}</p>
                    </div>
                    <div v-if="chat.image != null" class="balloon1-left">
                        <p>
                            <a target="_blank" v-bind:href="chat.image.image_url">
                                <img width="100" v-bind:src="chat.image.image_thumbnail_url">
                            </a>
                        </p>
                    </div>
                </div>
            </div>
            <div v-if="file != null" class="prepare_uploading_image">
                <img v-if="file != null" src="" width="30%" id="selected_image">
                <button v-on:click="uploadProfileImage">この画像をアップロードする</button>
            </div>
        </div>
    </section>
    <div class="talk_form">
        <div class="talk_form2">
            {{ Form::text("message", null, [
                "ref" => "message",
                "class" => "talk_form_text",
            ])}}

            <div class="">
                <input type="file" name="profile_image" id="uploadme" v-on:change="selectedFile" />
                <label class="uploadme2" for="uploadme"></label>
                <input type="submit" class="talk_form_submit" v-on:click="sendMessage">
            </div>
        </div>
    </div>
</div>
<script>
    var vm = new Vue({
        el: "#chat_area",
        data: {
            security_token: null,
            from_member_id: null,
            to_member_id: null,
            stack_chat_list: {},
            chat_list: [],
            file: null,
            file_reader: null,
            last_timeline_id: null,
            newest_timeline_id: null,
            timeline_id_list: [],
        },
        created: function() {

        },
        mounted: function() {
            this.from_member_id = this.$refs.from_member_id.value;
            this.to_member_id = this.$refs.to_member_id.value;
            this.security_token = this.$refs.security_token.value;
            console.dir(this.from_member_id);
            console.dir(this.to_member_id);
            this.getChatList(true, this.from_member_id, this.to_member_id, 0, 10, 1);
            var self =this;
            setInterval(function() {
                var max = self.timeline_id_list.reduce(function(a, b) {
                    return Math.max(a, b);
                });
                self.getChatList(false, self.from_member_id, self.to_member_id, max, 0, 1);
            }, 3500);
        },
        methods: {
            getOldMessage: function(is_scroll = false, from_member_id = null, to_member_id, timeline_id = null, limit = null, separator = null) {
                console.dir(this.timeline_id_list);
                var min = this.timeline_id_list.reduce(function(a, b) {
                    return Math.min(a, b);
                });
                this.getChatList(true, this.from_member_id, this.to_member_id, min, 10, -1);
            },
            getChatList: function(is_scroll = false, from_member_id = null, to_member_id, timeline_id = null, limit = null, separator = null) {
                var self = this;
                axios.get("/api/v1/timeline/message/" + from_member_id + "/" + to_member_id + "/" + timeline_id + "/" + limit + "/" + separator).then(function(response) {
                    var chat_list = response.data.response.timeline;
                    chat_list.forEach(function(value, index) {
                        if (separator > 0) {
                            if (self.timeline_id_list.indexOf(value.id) < 0) {
                                self.timeline_id_list.push(value.id);
                                // self.newest_timeline_id = chat_list[chat_list.length - 1].id;
                                self.chat_list.push(value);
                            }
                        } else {
                            if (self.timeline_id_list.indexOf(value.id) < 0) {
                                self.timeline_id_list.push(value.id);
                                // self.last_timeline_id = chat_list[0].id;
                                self.chat_list.unshift(value);
                            }
                        }
                    });
                }).then(function(data) {
                    // 最下部までスクロール
                    if (is_scroll === true && separator > 0) {
                        self.scrollToBottom();
                    }
                });
            },
            sendMessage: function() {
                this.message = this.$refs.message.value;
                var params = new URLSearchParams();
                params.append("from_member_id", this.from_member_id);
                params.append("to_member_id", this.to_member_id);
                params.append("security_token", this.security_token);
                params.append("message", this.message);
                var self = this;
                axios.post("/api/v1/timeline/message", params).then(function(response) {
                    if (response.data.status === true) {
                        alert(response.data.status);
                        $("input[name=message]").val("");
                        console.dir(response.data.response);
                        self.timeline_id_list.push(0);
                        var max = self.timeline_id_list.reduce(function(a, b) {
                            return Math.max(a, b);
                        });
                        self.getChatList(true, self.from_member_id, self.to_member_id, max, 0, 1);
                    }
                })
            },
            scrollToBottom: function () {
                var element = document.documentElement;
                var bottom = element.scrollHeight - element.clientHeight;
                window.scroll(0, bottom);
            },
            selectedFile: function (e) {
                // e => 選択したファイル要素のイベントオブジェクト
                this.file = e.target.files[0];
                this.file_reader = new FileReader();
                var self = this;
                this.file_reader.onload = function() {
                    $("img#selected_image").attr("src", this.result);
                }
                this.file_reader.readAsDataURL(this.file);
            },
            uploadProfileImage: function () {
                // 選択した画像をタイムライン上にアップロードする
                var params = new FormData();
                var use_type = 1; // 本人証明用申請書
                var is_approved = 1; // 証明書のスターテスを認証申請中にする
                var blur_level = 0; // 証明書の場合ぼかさない
                var member_id = this.from_member_id;
                params.append('profile_image', this.file);
                params.append("blur_level", blur_level);
                params.append("from_member_id", this.from_member_id);
                params.append("to_member_id", this.to_member_id);
                params.append("is_approved", is_approved);
                params.append("use_type", use_type);
                params.append("security_token", this.security_token);
                // input fileを空に
                $("input[name=profile_image]").val("");
                this.file = null;
                var self = this;
                axios.post("/api/v1/timeline/image", params).then(function(response) {
                    if (response.data.status === true) {
                        console.dir(response.data);
                        var max = self.timeline_id_list.reduce(function(a, b) {
                            return Math.max(a, b);
                        });
                        self.getChatList(true, self.from_member_id, self.to_member_id, max, 0, 1);
                    } else {
                        alert("本人証明書画像のアップロードに失敗しました。");
                    }
                }).catch(function(error) {
                    alert("本人証明書画像のアップロードに失敗しました。");
                    console.dir(error);
                });

            }
        }
    });
</script>
@include("member.common.footer")
