<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex">
    <title>管理画面ログイン 既婚者matchi</title>
    <meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1,viewport-fit=cover">
    <link rel="stylesheet" type="text/css" href="/admin/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/earlyaccess/notosansjp.css">
    <link rel="stylesheet" href="https://use.typekit.net/nki7ezk.css">
    <link rel="stylesheet" type="text/css" href="/admin/css/slick.css" />
    <link rel="stylesheet" type="text/css" href="/admin/css/slick-theme.css" />
    <link rel="stylesheet" href="/admin/css/drawer.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.7.1/css/lightbox.css" rel="stylesheet">

    <link rel="shortcut icon" type="image/x-icon" href="/admin/img/favicon.ico">
    <link rel="apple-touch-icon" href="/admin/img/favicon.ico">

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
</head>

<body class="drawer drawer--right">

    <button type="button" class="drawer-toggle drawer-hamburger">
        <!--変更予定-->
        <span class="sr-only">toggle navigation</span>
        <span class="drawer-hamburger-icon"></span>
    </button>
    <nav class="drawer-nav">
        <ul class="drawer-menu">
            <li><a href="/admin/members">会員一覧</a></li>
            <li><a href="/admin/MADADESU">身分証明書確認</a></li>
            <li><a href="/admin/MADADESU">管理スタッフ一覧</a></li>
            <li><a href="/admin/MADADESU">管理スタッフ登録</a></li>
    </nav>

    <header>
        <a href="/admin/top"><img src="/images/ttl.png" width="100"></a>
    </header>
    <section>
        <div class="box">
            <h2>Admin LOGIN</h2>
            <h5>管理画面ログイン</h5>

            <form>
                <p class="input_red">メールアドレスを入力してください</p>
                <input type="email" placeholder="メールアドレス">
                <p class="input_red">パスワードが正しくありません</p>
                <input type="password" placeholder="パスワード">
                <div class="btnbox">
                    <a href="/admin/top" class="btn">ログイン</a>
                </div>
            </form>
        </div>
        <!--box-->
    </section>

    <footer>
        ver.0.202100
    </footer>
    <script src="/admin/js/jquery-3.5.1.min.js"></script>
    <script src="/admin/js/iscroll.min.js"></script>
    <script src="/admin/js/drawer.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $(".drawer").drawer();
        });

    </script>
    <script type="text/javascript" src="/admin/js/slick.js"></script>
    <script>
        $(document).ready(function() {
            $('.face').slick({
                dots: true,
                infinite: true,
                speed: 300
            });
        });

    </script>

    <script>
        $(document).ready(function() {
            $('.opp_face').slick({
                dots: true,
                infinite: true,
                speed: 300
            });
        });

    </script>

    <script>
        $('.opp_imgs').slick({
            centerMode: true,
            dots: true,
            centerPadding: '64px',
            slidesToShow: 1,
            responsive: [{
                    breakpoint: 768,
                    settings: {
                        arrows: false,
                        centerMode: true,
                        centerPadding: '64px',
                        slidesToShow: 1
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        arrows: false,
                        centerMode: true,
                        centerPadding: '64px',
                        slidesToShow: 1
                    }
                }
            ]
        });

    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.7.1/js/lightbox.min.js" type="text/javascript">
    </script>
</body>

</html>
