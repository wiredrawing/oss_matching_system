@include("admin.common.header")

<section>
    <div class="box">
        <h2 class="noto">お知らせ</h2>
        <p class="cation">{{$error->getMessage()}}</p>
        <!-- {{$error->getLine()}} -->
        <!-- {{$error->getFile()}} -->
        <div class="tex_c">
            <p class="font12">
                <a class="button_to_back_previous_page" href="{{url()->previous()}}">前ページへ戻る</a>
            </p>
        </div>
    </div>
    <!--box-->
</section>
@include("admin.common.footer")
