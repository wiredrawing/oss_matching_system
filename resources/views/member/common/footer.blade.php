
@if (isset($request) && $request->member !== NULL)
<!-- ユーザーログイン中メニュー -->
<div class="footer_menu">
    <div class="footer_menu_in">
        <div class="footer_menu_icon">
            <a href="{{action("Member\\IndexController@index")}}">
            <img src="/images/footer_menu1.png">
            </a>
            <span>マイページ</span>
        </div>
        <div class="footer_menu_icon">
            @if (count($request->uncheck_timelines) > 0)
            <span class="pochi"><span class="pochi_in"></span></span>
            @endif
            <a href="{{action("Member\\MessageController@index")}}"><img src="/images/footer_menu2.png"></a>
            <span>メッセージ</span>
        </div>
        <div class="footer_menu_icon">
            @if (count($request->uncheck_logs) > 0)
            <span class="pochi"><span class="pochi_in"></span></span>
            @endif
            <a href="{{action("Member\\NoticeController@index")}}"><img src="/images/footer_menu3.png"></a>
            <span>お知らせ</span>
        </div>
        <div class="footer_menu_icon">
            @if (count($request->uncheck_footprints) > 0)
            <span class="pochi"><span class="pochi_in"></span></span>
            @endif
            <a href="{{action("Member\\FootprintController@index")}}"><img src="/images/footer_menu4.png"></a>
            <span>足跡</span>
        </div>
    </div>
</div>
<!--footer_menu-->
@endif

<footer>
</footer>
<script type="text/javascript">
    $(document).ready(function() {
        $(".drawer").drawer();
    });
    $(".backk").on("click", function (e) {
        e.preventDefault();
        history.back();
    });
    $(".button_to_back_previous_page").on("click", function (e) {
        e.preventDefault();
        history.back();
    })
</script>
</body>

</html>
