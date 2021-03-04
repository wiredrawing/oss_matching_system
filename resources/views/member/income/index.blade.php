@include("member.common.header")
<!--ログイン後-->
<section>
    <div class="box">
        <h2 class="noto">収入証明書確認</h2>
        @if ($request->member->income_image !== NULL && (int)$request->member->income_image->is_approved === Config("const.image.approve_type.authenticated"))
        <p class="cation">
            収入証明書確認が完了致しました。
        </p>
        @elseif ($request->member->income_image !== NULL && (int)$request->member->income_image->is_approved === Config("const.image.approve_type.applying"))
        <p class="cation">
            現在、審査中です。しばらくお待ち下さい。
        </p>
        @elseif ($request->member->income_image !== NULL && (int)$request->member->income_image->is_approved === Config("const.image.approve_type.rejected"))
        <p class="cation">
            収入証明書が拒否されました。再度正しい収入証明書をアップロードして下さい。
        </p>
        <div class="tenpu">
            <img src="" id="selected_image">
        </div>
        {{ Form::input('file', 'profile_image', null, [
            'size' => 30,
        ]) }}
        <div class="btnbox mt32">
            <a href="" class="btn" id="upload_image">収入証明書確認用の画像を送る</a>
        </div>
        @else
        <p class="cation">収入証明書確認できる書類を撮影し、お送りください。「VIP」のバッジが表示されるようになり安心してご利用いただけます。</p>
        <div class="tenpu">
            <img src="" id="selected_image">
        </div>
        {{ Form::input('file', 'profile_image', null, [
            'size' => 30,
        ]) }}
        <p class="cation">収入証明書確認できる書類を撮影し、お送りください。
            一度、収入証明書確認の申請を行うと、結果が帰ってくるまで再度の申請依頼ができません。
            正しく収入証明書確認が可能な証明書をアップロードして下さい。
        </p>
        <div class="btnbox mt32">
            <a href="" class="btn" id="upload_image">収入証明確認用の画像を送る</a>
        </div>
        @endif
    </div>
    <!--box-->
</section>
<!--ログイン後-->

<script>
    $(function() {
        $("input[name=profile_image]").on("change", function(e) {
            console.dir(e);
            var file = e.target.files[0];
            var file_reader = new FileReader();
            file_reader.onload = function() {
                console.dir(this);
                $("img#selected_image").attr("src", this.result);
            }
            file_reader.readAsDataURL(file);
        });

        // POST処理
        $("#upload_image").on("click", function(e) {
            e.preventDefault();
            var params = new FormData();
            var use_type = {{$use_type}}; // 本人証明用申請書
            var is_approved = {{$is_approved}}; // 証明書のスターテスを認証申請中にする
            var blur_level = {{$blur_level}}; // 証明書の場合ぼかさない
            var member_id = {{$request->member->id}};
            console.dir($("input[name=profile_image]").eq(0));
            params.append('profile_image', $("input[name=profile_image]")[0].files[0]);
            params.append("blur_level", blur_level);
            params.append("member_id", member_id);
            params.append("is_approved", is_approved);
            params.append("use_type", use_type);
            axios.post("/public/api/v1/media/image", params).then(function(response) {
                if (response.data.status === true) {
                    location.href =
                        "{{ action('Member\\IndexController@income') }}";
                    $("input[name=profile_image]").hide();
                    $("img#selected_image").attr("src", response.data.response.url);
                } else {
                    alert("収入証明書画像のアップロードに失敗しました。");
                }
            }).catch(function(error) {
                alert("収入証明書画像のアップロードに失敗しました。");
                console.dir(error);
            });
        });
    })

</script>
@include("member.common.footer")
