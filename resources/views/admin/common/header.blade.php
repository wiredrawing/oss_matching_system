<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex">
    <title>管理画面トップ 既婚者matchi</title>
    <meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1,viewport-fit=cover">
    <link rel="stylesheet" type="text/css" href="/admin/css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/earlyaccess/notosansjp.css">
    <link rel="stylesheet" href="https://use.typekit.net/nki7ezk.css">
    <link rel="stylesheet" type="text/css" href="/admin/css/slick.css" />
    <link rel="stylesheet" type="text/css" href="/admin/css/slick-theme.css" />
    <link rel="stylesheet" href="/admin/css/drawer.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.7.1/css/lightbox.css" rel="stylesheet">
    <link rel="shortcut icon" type="image/x-icon" href="/admin/img/favicon.ico">
    <link rel="apple-touch-icon" href="/admin/img/favicon.ico">
    <script src="/admin/js/jquery-3.5.1.min.js"></script>
    <script src="/admin/js/iscroll.min.js"></script>
    <script src="/admin/js/drawer.min.js"></script>
    <script type="text/javascript" src="/admin/js/slick.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.7.1/js/lightbox.min.js" type="text/javascript"></script>
    <script src="/js/axios.min.js"></script>
    <script src="/js/vue@2.6.12.js"></script>
</head>

<body class="drawer drawer--right">
    <button type="button" class="drawer-toggle drawer-hamburger">
        <!--変更予定-->
        <span class="sr-only">toggle navigation</span>
        <span class="drawer-hamburger-icon"></span>
    </button>
    <nav class="drawer-nav">
        <ul class="drawer-menu">
            @if (isset($member))
            <li><a href="{{ action('Admin\\MemberController@sendingLike', ['member_id' => $member->id]) }}">贈ったGood</a>
            <li><a href="{{ action('Admin\\MemberController@gettingLike', ['member_id' => $member->id]) }}">もらったGood</a></li>
            <li><a href="{{ action('Admin\\MemberController@matching', ['member_id' => $member->id]) }}">相互マッチ済み</a></li>
            <li><a href="{{ action('Admin\\MemberController@footprint', ['member_id' => $member->id]) }}">足跡</a></li>
            <li><a href="{{ action('Admin\\MemberController@visit', ['member_id' => $member->id]) }}">つけた足跡</a></li>
            <li><a href="{{ action('Admin\\MemberController@image', ['member_id' => $member->id]) }}">アップロード済み画像</a></li>
            @endif
            <li><a href="{{ action("Admin\\MemberController@index") }}">会員一覧</a></li>
            <li><a href="{{ action("Admin\\IdentityController@index") }}">身分証明書確認({{count($request->users_applying_identity)}})</a></li>
            <li><a href="{{ action("Admin\\IncomeController@index") }}">収入証明書確認({{count($request->users_applying_income)}})</a></li>
            <li><a href="{{action("Admin\\ViolationController@index")}}">違反通報一覧</a></li>
            <li><a href="{{action("Admin\\PaymentController@index")}}">支払い確定済み履歴</a></li>
            <li><a href="{{action("Admin\\PaymentController@canceled")}}">解約済み履歴</a></li>
            <li><a href="{{action("Admin\\WithdrawController@index")}}">退会済み履歴</a></li>
            <li><a href="{{action("Admin\\StaffController@index")}}">管理スタッフ一覧</a></li>
            <li><a href="{{action("Admin\\StaffController@create")}}">管理スタッフ登録</a></li>
            <li><a href="{{action("Admin\\LoginController@logout")}}">ログアウト</a></li>
        </ul>
    </nav>
    <header>
        <a href="/admin/top"><img src="/images/ttl.png" width="100"></a>
    </header>
