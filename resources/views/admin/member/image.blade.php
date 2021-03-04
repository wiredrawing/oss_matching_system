@include("admin.common.header")
<section class="wrapp">
    <div class="admin_side">
        @include("admin.common.member_menu")
    </div>
    <!--admin_side-->

    <div class="mypagewrap">
        <div class="mypagebox">
            <p class="tex_c">{{$request->administrator->display_name}}さんログイン中</p>
            <h2>{{$member->display_name}}さんのアップロードした画像</h2>
        </div>
        <!--box-->
        <div class="mypagebox2 pt0">
            @if (isset($images) && $images->count() > 0)
            <table>
                <tr class="items">
                    <th>ID</th>
                    <td>画像URL</td>
                    <td>投稿日時</td>
                    <td>画像タイプ</td>
                    <td>認証状態(※1)</td>
                    <td>削除日時</td>
                    <td>削除</td>
                </tr>
                @foreach ($images as $key => $value)
                <tr>
                    <th>{{$value->id}}</th>
                    <td><a href="{{$value->image_url}}" data-lightbox="loadedimg" rel="lightbox[group]">
                        <button>画像を確認</button>
                    </a></td>
                    <td>
                        {{$value->created_at->format("Y年m月j日")}}<br>
                        {{$value->created_at->format("H時i分s秒")}}
                    </td>
                    <td>{{$use_type_name[$value->use_type]}}</td>
                    <td>{{$approve_type_name[$value->is_approved]}}</td>
                    <td>@if (isset($value->deleted_at))
                        ({{$value->deleted_at->format("Y年m月j日")}})<br>
                        ({{$value->deleted_at->format("H時i分s秒")}})
                        @endif
                    </td>
                    <td>
                        @if ($value->deleted_at === NULL)
                        {{Form::open([
                            "url" => action("Admin\\MemberController@deleteImage", [
                                "member_id" => $member->id,
                            ]),
                            "method" => "POST",
                            "class" => "delete_image_form",
                        ])}}
                        {{Form::hidden("token", $value->token)}}
                        {{Form::hidden("image_id", $value->id)}}
                        <button class="delete_image_button">削除する</button>
                        {{Form::close()}}
                        @else
                        削除済み
                        @endif
                    </td>
                </tr>
                @endforeach
            </table>
            @endif
        </div>
        <p class="tex_c mt64">※1)認証が必要な画像のみ、表示されます。</p>
    </div>
    <!--mypagewrap-->
</section>
<script>
    // 画像削除用フォームのイベント
    $(".delete_image_button").each(function (index) {
        $(this).on("click", function(e) {
            if (confirm("指定した画像を削除します｡よろしいですか?")) {
                $(".delete_image_button").eq(index).trigger("submit");
            }
        });
    });
</script>
@include("admin.common.footer")
