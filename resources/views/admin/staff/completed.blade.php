@include("admin.common.header")
<section class="wrapp">
    <div class="admin_side">
        @include("admin.common.member_menu")
    </div>
    <!--admin_side-->

    <div class="mypagewrap">
        <div class="mypagebox">
            <p class="tex_c">{{$request->administrator->display_name}}さんログイン中</p>
            <p>新しい運営者アカウントの新規登録が完了しました｡</p>
        </div>
    </div>
    <!--mypagewrap-->
</section>
@include("admin.common.footer")
