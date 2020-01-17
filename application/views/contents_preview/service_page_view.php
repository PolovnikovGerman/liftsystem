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
    <link rel="stylesheet" type="text/css" href="/css/contents_preview/jquery-ui.css"/>
    <!-- <link rel="stylesheet" type="text/css" href="/css/animate.min.css"> -->
    <link rel="stylesheet" type="text/css" href="/css/contents_preview/services.css?r=<?= getsitecssversion() ?>"/>
    <script type="text/javascript" src="/js/contents_preview/jquery.min.js"></script>
    <script type="text/javascript" src="/js/contents_preview/jquery-ui.min.js"></script>
    <script type="text/javascript" src="/js/contents_preview/jquery.bt.js"></script>
</head>
<body>
<header><?= $header ?></header>
<div class="main-content">
    <div class="container">
        <!-- container -->
        <div class="body-site contact-page">
            <div class="body-site service-page">
                <div class="service-banner">
                    <div class="title-banner">
                        <h3><?=$contents['service_maintitle']?></h3>
                        <h4><?=$contents['service_secondarytext']?></h4>
                    </div>
                    <div class="info-banner" style="background: url(<?=$contents['service_mainimage']?>)">
                        <p><?=nl2br($contents['service_belowimagetext'])?></p>
                    </div>
                </div>

                <div class="service-list">
                    <div class="service-item">
                        <div class="service-info">
                            <h4><?=$contents['service_title1']?></h4>
                            <p><?=nl2br($contents['service_text1'])?></p>
                        </div>
                        <div class="service-image left">
                            <img src="<?=$contents['service_image1']?>">
                        </div>
                    </div>
                    <div class="service-item">
                        <div class="service-image right">
                            <img src="<?=$contents['service_image2']?>">
                        </div>
                        <div class="service-info">
                            <h4><?=$contents['service_title2']?></h4>
                            <p><?=nl2br($contents['service_text2'])?></p>
                        </div>
                    </div>
                    <div class="service-item">
                        <div class="service-info">
                            <h4><?=$contents['service_title3']?></h4>
                            <p><?=nl2br($contents['service_text3'])?></p>
                        </div>
                        <div class="service-image left">
                            <img src="<?=$contents['service_image3']?>">
                        </div>
                    </div>
                    <div class="service-item">
                        <div class="service-image right">
                            <img src="<?=$contents['service_image4']?>">
                        </div>
                        <div class="service-info">
                            <h4><?=$contents['service_title4']?></h4>
                            <p><?=nl2br($contents['service_text4'])?></p>
                        </div>
                    </div>
                    <div class="service-item">
                        <div class="service-info">
                            <h4><?=$contents['service_title5']?></h4>
                            <p><?=nl2br($contents['service_text5'])?></p>
                        </div>
                        <div class="service-image left">
                            <img src="<?=$contents['service_image5']?>"/>
                        </div>
                    </div>
                    <div class="service-item">
                        <div class="service-image right">
                            <img src="<?=$contents['service_image6']?>">
                        </div>
                        <div class="service-info">
                            <h4><?=$contents['service_title6']?></h4>
                            <p><?=nl2br($contents['service_text6'])?></p>
                        </div>
                    </div>
                    <div class="service-item">
                        <div class="service-info">
                            <h4><?=$contents['service_title7']?></h4>
                            <p><?=nl2br($contents['service_text7'])?></p>
                        </div>
                        <div class="service-image left">
                            <img src="<?=$contents['service_image7']?>">
                        </div>
                    </div>
                    <div class="service-item">
                        <div class="service-image right">
                            <img src="<?=$contents['service_image8']?>">
                        </div>
                        <div class="service-info">
                            <h4><?=$contents['service_title8']?></h4>
                            <p><?=nl2br($contents['service_text8'])?></p>
                        </div>
                    </div>
                </div>

                <div class="contact-call">
                    <p>For more info, call us today:</p>
                    <p class="phone">1-800-790-6090</p>
                    <p>or</p>
                    <div class="button">
                        <a href="/" class="bnt-start">Return to Shopping <span><img src="/img/page_modern/long-arrow-right-write.svg"></span></a>
                    </div>
                </div>
                <?php // echo $sendquestion ?>
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
</div>
<div class="footer-bg"><?= $footer ?></div>
</body>
</html>
