@include("member.common.header")
<!--ログイン後-->
<section>
    <div class="box">
        <h2 class="noto">本人確認</h2>
        @if ($request->member->identity_image !== NULL && (int)$request->member->identity_image->is_approved === Config("const.image.approve_type.authenticated"))
        <p class="cation">
            本人確認が完了致しました。
        </p>
        @elseif ($request->member->identity_image !== NULL && (int)$request->member->identity_image->is_approved === Config("const.image.approve_type.applying"))
        <p class="cation">
            現在、審査中です。しばらくお待ち下さい。
        </p>
        @elseif ($request->member->identity_image !== NULL && (int)$request->member->identity_image->is_approved === Config("const.image.approve_type.rejected"))
        <p class="cation">
            本人確認証明書が拒否されました。再度正しい本人確認証明書をアップロードして下さい。
        </p>
        <div class="tenpu">
            <img src="" id="selected_image">
        </div>
        {{ Form::input('file', 'profile_image', null, [
            'size' => 30,
        ]) }}
        <div class="btnbox mt32">
            <a href="" class="btn" id="upload_image">本人確認用の画像を送る</a>
        </div>
        @else
        <p class="cation">本人確認できる書類を撮影し、お送りください。「本人確認済み」のバッジが表示されるようになり安心してご利用いただけます。<br>一度、本人確認証明書の申請を行うと、結果が帰ってくるまで再度の申請依頼ができません。
            正しく本人確認が可能な証明書をアップロードして下さい。</p>
        <div class="tenpu">
            <img src="" id="selected_image">
        </div>
        {{ Form::input('file', 'profile_image', null, [
            'size' => 30,
        ]) }}
        
        
        
        
       
        
        <div class="btnbox mt32">
            <a href="" class="btn" id="upload_image">本人確認用の画像を送る</a>
        </div>
        
         <div class="">
	        <img src="/images/licence.jpg">
	        <h4>赤枠の部分が確認できるように撮影をお願いいたします。</h4>
	        <p class="cation mb32">
	        ・生年月日の部分<br>
・「運転免許証」の名称部分<br>
・発行元の名称部分</p>
        </div>
        <img src="/images/licence2.jpg">
        
        <h4>そのほかの場合、下記書類のうち、いずれかのお写真が必要となります。</h4>
	        <p>健康保険証/パスポート/マイナンバーカード／住民基本台帳カード／年金手帳／特別永住者証明書／在留カードなど</p>
	        
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
                        "{{ action('Member\\IndexController@identity') }}";
                    $("input[name=profile_image]").hide();
                    $("img#selected_image").attr("src", response.data.response.url);
                } else {
                    alert("本人証明書画像のアップロードに失敗しました。");
                }
            }).catch(function(error) {
                alert("本人証明書画像のアップロードに失敗しました。");
                console.dir(error);
            });
        });
    })

</script>
@include("member.common.footer")
