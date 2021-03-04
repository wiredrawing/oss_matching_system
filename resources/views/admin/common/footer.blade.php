<script type="text/javascript">
    $(document).ready(function() {
        $(".drawer").drawer();

        $('.face').slick({
            dots: true,
            infinite: true,
            speed: 300
        });
        $('.opp_face').slick({
            dots: true,
            infinite: true,
            speed: 300
        });

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
    });
</script>
</body>
</html>
