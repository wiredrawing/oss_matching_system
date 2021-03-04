@include("admin.common.header")
<section class="wrapp">

    <div class="admin_side">
        @include("admin.common.member_menu")
    </div>
    <!--admin_side-->

    <div class="mypagewrap">
        <div class="mypagebox">
            <p class="tex_c">{{$request->administrator->display_name}}さんログイン中</p>
            <h2>{{$member->display_name}}さんの詳細情報</h2>
        </div>
        <!--box-->
        <div class="mypagebox">
            <p>{{$member->display_name}}さんの詳細情報の更新が完了しました｡</p>
        </div>
    </div>
    <!--mypagewrap-->
</section>
<script>
    $(function () {
        $(".update_button").on("click", function (e) {
            e.preventDefault();
            $("form").trigger("submit");
        });
    })
</script>
@include("admin.common.footer")
