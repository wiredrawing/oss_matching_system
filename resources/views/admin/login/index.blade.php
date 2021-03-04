<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex">
    <title>管理画面ログイン 既婚者matchi</title>
    <meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1,viewport-fit=cover">
    <link rel="stylesheet" type="text/css" href="/admin/css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/earlyaccess/notosansjp.css">
    <link rel="stylesheet" href="https://use.typekit.net/nki7ezk.css">
    <link rel="stylesheet" type="text/css" href="/admin/css/slick.css" />
    <link rel="stylesheet" type="text/css" href="/admin/css/slick-theme.css" />
    <link rel="stylesheet" href="/admin/css/drawer.css">
    <script src="/admin/js/jquery-3.5.1.min.js"></script>
    <script src="/admin/js/jquery-3.5.1.min.js"></script>
    <script src="/admin/js/iscroll.min.js"></script>
    <script src="/admin/js/drawer.min.js"></script>
    <script type="text/javascript" src="/admin/js/slick.js"></script>
</head>

<body class="drawer drawer--right">


    <header>
        <a href="/mypage"><img src="/images/ttl.png" width="100"></a>
    </header>
    <section>
        <div class="box">
            <h2>Admin LOGIN</h2>
            <h5>管理画面ログイン</h5>

            {{ Form::open([
                'url' => action('Admin\\LoginController@authenticate'),
                'method' => 'POST',
            ]) }}
            @if ($errors->has('email'))
                <p class="input_red">{{ $errors->first('email') }}</p>
            @endif
            {{ Form::email('email', '', [
                'placeholder' => 'ログイン用メールアドレス',
            ]) }}
            @if ($errors->has('password'))
                <p class="input_red">{{ $errors->first('password') }}</p>
            @endif
            {{ Form::password('password', [
                'placeholder' => 'ログイン用パスワード',
            ]) }}
            <div class="btnbox">
                <a class="btn">ログイン</a>
            </div>
            {{ Form::close() }}
        </div>
        <!--box-->
    </section>
    <script>
        $(function () {
            $(".btn").on("click", function(e) {
            e.preventDefault();
            $("form").eq(0).trigger("submit");
        });
        })
    </script>
</body>
</html>
