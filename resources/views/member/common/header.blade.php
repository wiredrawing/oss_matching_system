<!DOCTYPE html>
<html lang="ja">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="robots" content="noindex">
    <title>{{$request->title}} | 既婚者マッチングStyle</title>
    <meta name="keywords" content="{{$request->keywords}}">
    <meta name="description" content="{{$request->description}}">
    <meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1,viewport-fit=cover">
    <link rel="stylesheet" href="https://fonts.googleapis.com/earlyaccess/notosansjp.css">
    <link rel="stylesheet" href="https://use.typekit.net/nki7ezk.css">
    <link rel="stylesheet" type="text/css" href="/css/style.css">
    <link rel="stylesheet" type="text/css" href="/css/slick.css" />
    <link rel="stylesheet" type="text/css" href="/css/slick-theme.css" />
    <link rel="stylesheet" type="text/css" href="/css/drawer.css">
    <link rel="shortcut icon" type="image/x-icon" href="/img/favicon.ico">
    <link rel="apple-touch-icon" href="/img/favicon.ico">
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:site" content="" />
    <meta property="og:url" content="" />
    <meta property="og:title" content="" />
    <meta property="og:description" content="" />
    <meta property="og:image" content="" />
    <link rel="canonical" href="">
    <!-- ここからOGP -->
    <meta property="og:title" content="">
    <meta property="og:type" content="article">
    <meta property="og:description" content="">
    <script src="/js/jquery-3.5.1.min.js"></script>
    <script src="/js/iscroll.min.js"></script>
    <script src="/js/drawer.min.js"></script>
    <script src="/js/slick.js" type="text/javascript"></script>
    <script src="/js/axios.min.js"></script>
    <script src="/js/vue@2.6.12.js"></script>
    <script src="/js/vue-carousel.min.js"></script>
</head>

<body class="drawer drawer--right">

    <button type="button" class="drawer-toggle drawer-hamburger">
        <!--変更予定-->
        <span class="sr-only">toggle navigation</span>
        <span class="drawer-hamburger-icon"></span>
    </button>


    @if (isset($request) && $request->member !== null)
    <nav class="drawer-nav">
        <div class="side_profile">
            <div class="side_profile1">
                <div class="side_profile1a">
                    <a href="{{action("Member\\IndexController@index")}}" class="side_profile1b">
                        @if ($request->member->profile_images->count() > 0)
                        <img src="{{action("Api\\v1\\MediaController@show", [
                            "image_id" => $request->member->profile_images[0]->id,
                            "token" => $request->member->profile_images[0]->token,
                        ])}}">
                        @else
                        <img src="/images/sample_user.jpg">
                        @endif
                    </a>
                </div>
                <a href="{{action("Member\\IndexController@index")}}" class="side_profile1b">{{$request->member->display_name}}</a>
            </div>
            <!--side_profile1-->
            <div class="side_profile2">
                <!--<a href="{{action("Member\\LikeController@sendLike")}}" class="side_profile2a">{{$request->number_of_sending_likes}} 贈ったGood</a>-->
                <a href="{{action("Member\\LikeController@getLike")}}" class="side_profile2b">{{$request->number_of_getting_likes}} もらったGood</a>
                <a href="{{action("Member\\LikeController@matching")}}" class="side_profile2a">{{$request->number_of_matching_users}} 相互マッチ</a>
                <!--<p class="side_profile2b">{{$request->basic["prefecture"][$request->member->prefecture]}}</p>-->
            </div>
            <!--side_profile2-->
        </div>
        <!--side_profile-->
        <ul class="drawer-menu">
            {{-- <li class="side_input"><input type="text" placeholder="相手を探す"></li> --}}
            <li><a href="{{action("Member\\SearchController@index")}}">条件検索</a></li>
            <li><a href="{{action("Member\\MessageController@index")}}">メッセージ</a></li>
            <li><a href="{{action("Member\\FootprintController@index")}}">足跡</a></li>
            <li><a href="{{action("Member\\NoticeController@index")}}">お知らせ</a></li>
            <li><a href="{{action("Member\\IndexController@edit")}}">プロフィール編集</a></li>
            @if ($request->member->gender === "M")
            <li><a href="{{action("Member\\SubscribeController@index")}}">有料プラン新規･変更</a></li>
            @endif
            <li><a href="{{action("Member\\ConfigController@index")}}">設定</a></li>
            <li><a href="{{action("Member\\LoginController@logout")}}">ログアウト</a></li>
            <span class="sub_line"></span>
            <li class="mini">{{$request->member->price_plan->name}}</li>
            @if ($request->member->identity_image !== NULL && (int)$request->member->identity_image->is_approved === Config("const.image.approve_type.authenticated"))
            <li class="mini"><img src="/images/checkicon.png" width="12"> 本人確認済</li>
            @else
            <li class="mini"><a href="{{action("Member\\IndexController@identity")}}"><img src="/images/caitionicon.png" width="12"> 本人確認証明書アップロード</a></li>
            @endif

            @if ($request->member->gender === "M")
                <!-- 収入両名は男性のみ対応 -->
                @if ($request->member->income_image !== NULL && (int)$request->member->income_image->is_approved === Config("const.image.approve_type.authenticated"))
                <li class="mini"><img src="/images/checkicon.png" width="12"> 収入証明確認済</li>
                @else
                <li class="mini"><a href="{{action("Member\\IndexController@income")}}"><img src="/images/caitionicon.png" width="12"> 収入証明アップロード</a></li>
                @endif
            @endif
        </ul>
    </nav>
    @else
    <nav class="drawer-nav">
        <ul class="drawer-menu">
            <li><a href="{{action("Member\\LoginController@index")}}">ログイン</a></li>
            <li><a href="{{action("Member\\EmailController@index")}}">新規会員登録</a></li>
            <span class="sub_line"></span>
            <li><a href="/terms">会員規約</a></li>
            <li><a href="/info">特定商取引法に基づく表記</a></li>
            <li><a href="/privacy">プライバシーポリシー</a></li>
        </ul>
    </nav>
    @endif

    <header>
        <a href="{{action("Member\\IndexController@index")}}"><img src="/images/ttl.png" width="100"></a>
    </header>
