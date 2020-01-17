<!DOCTYPE html>
<html>
<head>
    <title><?= $meta['meta_title'] ?></title>
    <!-- <meta http-equiv="cache-control" content="no-cache" /> -->
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta name="keywords" content="<?= $meta['meta_keywords'] ?>"/>
    <meta name="description" content="<?= $meta['meta_description'] ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="google-site-verification" content="75ZHtQuQCCPEcJkOytOEe6I1-6PvF9m3axiqBxVdtx8"/>
    <link rel="stylesheet" type="text/css" href="/css/contents_preview/main.css?r=<?= getsitecssversion() ?>"/>
    <link rel="stylesheet" type="text/css" href="/css/jquery-ui.css"/>
    <!-- <link rel="stylesheet" type="text/css" href="/css/animate.min.css"> -->
    <link rel="stylesheet" type="text/css" href="/css/contents_preview/terms.css?r=<?= getsitecssversion() ?>"/>
    <script type="text/javascript" src="/js/jquery.min.js"></script>
    <script type="text/javascript" src="/js/jquery-ui.min.js"></script>
    <script type="text/javascript" src="/js/jquery.bt.js"></script>
    <script type="text/javascript">
        var lnkoffset = 95;
        $(document).ready(function () {
            $("a.tp-item").click(function () {
                var aid = $(this).data('item');
                scrollToAnchor(aid);
            });
        });

        function scrollToAnchor(aid) {
            var aTag = $("a[name='" + aid + "']");
            $('html,body').animate({scrollTop: aTag.offset().top - lnkoffset}, 'slow');
        }
    </script>
</head>
<body>
<header><?= $header ?></header>
<div class="main-content">
    <div class="container">

        <div class="body-site tp-page">

            <h3 class="title-page"><?=$contents['term_maintitle']?></h3>
            <p><?=$contents['term_smalltext']?></p>

            <div class="tp-body">
                <div class="headline-tp">
                    <?php $numrow = 1; ?>
                    <?php $headtp = 1; ?>
                    <ul class="headline-tp-1">
                        <?php foreach ($terms as $row) { ?>
                        <li><a href="#tp-item-<?= $row['term_id'] ?>" onclick="return false;" class="tp-item"
                               data-item="tp-item-<?= $row['term_id'] ?>"><?= $row['term_header'] ?></a></li>
                        <?php $numrow++; ?>
                        <?php if ($numrow > $maxnum) { ?>
                    </ul>
                    <?php $headtp++;
                    $numrow = 1; ?>
                    <ul class="headline-tp-<?= $headtp ?>">
                        <?php } ?>
                        <?php } ?>
                    </ul>
                </div>
            </div>

            <p class="notes">If you have any questions please either refer to our <a href="/faq.html">FAQ</a> page or <a
                    href="/contactus.html">Contact Us</a> directly.</p>

            <div class="tp-all-text">
                <?php foreach ($terms as $row) { ?>
                    <h4><a name="tp-item-<?= $row['term_id'] ?>"></a><?= $row['term_header'] ?></h4>
                    <p><?= $row['term_text'] ?></p>
                <?php } ?>
            </div>

            <p class="notes">Please feel free to contact us if you have additional questions.</p>
            <div class="top-brands">
                <h3>The World’s Top Brands Trust Us For A Reason</h3>
                <ul>
                    <li><img src="/img/homepage/brand-1.jpg"></li>
                    <li><img src="/img/homepage/brand-2.jpg"></li>
                    <li><img src="/img/homepage/brand-3.jpg"></li>
                    <li><img src="/img/homepage/brand-4.jpg"></li>
                </ul>
            </div>

            <div class="special-deals">
                <div class="blue-bg">
                    <img src="/img/page_modern/special_dials.png" class="title">
                    <div id="specialdialplace">
                        <span class="sucesssignup">Success! You are now registered!</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="footer-bg"><?= $footer ?></div>
</body>
</html>