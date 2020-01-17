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
    <link rel="stylesheet" type="text/css" href="/css/contents_preview/faqpage.css?r=<?= getsitecssversion() ?>"/>
    <script type="text/javascript" src="/js/jquery.min.js"></script>
    <script type="text/javascript" src="/js/jquery-ui.min.js"></script>
    <script type="text/javascript" src="/js/jquery.bt.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.example1').bt({
                ajaxCache: false,
                positions: 'right',
                width: '360px',
                padding: 15,
                cornerRadius: 10,
                fill: '#FFFFFF',
                cssStyles: {color: '#FFF'},
                ajaxPath: ["$(this).attr('href')"]
            });
            $('.example2').bt({
                ajaxCache: false,
                positions: 'left',
                width: '360px',
                padding: 15,
                cornerRadius: 10,
                fill: '#FFFFFF',
                cssStyles: {color: '#FFF'},
                ajaxPath: ["$(this).attr('href')"]
            });
        })
    </script>
</head>
<body>
<header><?= $header ?></header>
<div class="main-content">
    <div class="container">

        <div class="body-site faq-page">
            <h3 class="title-page"><?= $contents['faq_maintitle'] ?></h3>
            <p><?= $contents['faq_mainbody'] ?></p>
            <p class="notes"><?= $contents['faq_helptext'] ?></p>

            <div class="questions-lists">
                <div class="list-one">
                    <div class="title-box"><h4>Ordering <span>Questions</span></h4></div>
                    <ul>
                        <?php foreach ($faq['ordering'] as $row) { ?>
                            <li><a class="example1"
                                   href="/content/faq_preview_details?id=<?= $row['faq_id'] ?>&ver=<?= $version ?>&part=ordering"
                                   onclick="return false;"><?= $row['faq_quest'] ?></a></li>
                        <?php } ?>
                    </ul>
                </div>
                <div class="list-two">
                    <div class="title-box"><h4>Artwork <span>Questions</span></h4></div>
                    <ul>
                        <?php foreach ($faq['artwork'] as $row) { ?>
                            <li><a class="example2"
                                   href="/content/faq_preview_details?id=<?= $row['faq_id'] ?>&ver=<?= $version ?>&part=artwork"
                                   onclick="return false;"><?= $row['faq_quest'] ?></a></li>
                        <?php } ?>
                    </ul>
                </div>
                <div class="list-tree">
                    <div class="title-box"><h4>Product <span>Questions</span></h4></div>
                    <ul>
                        <?php foreach ($faq['product'] as $row) { ?>
                            <li><a class="example1"
                                   href="/content/faq_preview_details?id=<?= $row['faq_id'] ?>&ver=<?= $version ?>&part=product"
                                   onclick="return false;"><?= $row['faq_quest'] ?></a></li>
                        <?php } ?>
                    </ul>
                </div>
                <div class="list-four">
                    <div class="title-box"><h4>General <span>Questions</span></h4></div>
                    <ul>
                        <?php foreach ($faq['general'] as $row) { ?>
                            <li><a class="example2"
                                   href="/content/faq_preview_details?id=<?= $row['faq_id'] ?>&ver=<?= $version ?>&part=general"
                                   onclick="return false;"><?= $row['faq_quest'] ?></a></li>
                        <?php } ?>
                    </ul>
                </div>
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
                    <img src="/img/special_dials.png" class="title">
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