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
    <link rel="stylesheet" type="text/css" href="/css/contents_preview/aboutus.css?r=<?= getsitecssversion() ?>"/>
    <script type="text/javascript" src="/js/jquery.min.js"></script>
    <script type="text/javascript" src="/js/jquery-ui.min.js"></script>
    <script type="text/javascript" src="/js/jquery.bt.js"></script>
</head>
<body>
<header><?= $header ?></header>
<div class="main-content">
    <div class="container">
        <!-- container -->
        <div class="body-site contact-page">
            <div class="about-text">
                <h3><?=$contents['about_maintitle']?></h3>
                <p><?=nl2br($contents['about_mainbodybodytext'])?></p>
            </div>
            <div class="img-about">
                <?php if (!empty($contents['about_mainimage'])) { ?>
                    <img src="<?=$contents['about_mainimage']?>">
                <?php } ?>
            </div>
            <div class="about-contact">
                <div class="about-contact-left">
                    <div class="about-contact-info">
                        <h4>Visit Us:</h4>
                        <p><?=nl2br($address['address_visit'])?></p>
                        <br>
                        <a href="javascript:void(0);" class="googleshow">Find us on Google Maps</a>
                        <div class="grey-line"></div>
                        <h4>Contact Us:</h4>
                        <p>Toll Free: <?=$address['address_phone']?><br>
                            Int'l Call: <?=$address['address_phonelocal']?><br>
                            Fax: <?=$address['address_fax']?><br>
                            Email: <a href="mailto:<?=$address['address_email']?>"><?=$address['address_email']?></a><br>
                            Live Chat: <a href="http://messenger.providesupport.com/messenger/1ksls0kzbgwwk1sj20g2zyuqfs.html" target="_blank" onclick="return false;" class="connectlivechat">click here</a>
                        </p>
                        <div class="grey-line"></div>
                        <div class="hours_affilations">
                            <h4>Our Hours:</h4>
                            <p><?=$address['address_hours']?><br><?=$address['address_days']?></p>
                        </div>
                        <div class="hours_affilations">
                            <?php if (!empty($contents['about_affilationsrc1']) || !empty($contents['about_affilationsrc2'])) { ?>
                                <h4>AFFILIATIONS:</h4>
                                <?php if (!empty($contents['about_affilationsrc1'])) { ?>
                                    <img src="<?=$contents['about_affilationsrc1']?>">
                                <?php } ?>
                                <?php if (!empty($contents['about_affilationsrc2'])) { ?>
                                    <img src="<?=$contents['about_affilationsrc2']?>">
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="about-contact-right">
                    <div class="pc-block">
                        <div class="title-pc"><?=$contents['about_inboxtitle']?></div>
                        <div class="body-pc">
                            <p><?=nl2br($contents['about_inboxtext'])?></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="testimonials-block">
                <div class="testimonials-block-title">
                    <h3><?=$contents['about_testimonialstitle']?></h3>
                    <div class="google-rating-testimonials">
                        <img src="/img/google-logo-white.png">
                        <span class="rating">4.8</span>
                        <div class="stars">
                            <span class="star"><img src="/img/page_modern/star-yellow.svg"></span>
                            <span class="star"><img src="/img/page_modern/star-yellow.svg"></span>
                            <span class="star"><img src="/img/page_modern/star-yellow.svg"></span>
                            <span class="star"><img src="/img/page_modern/star-yellow.svg"></span>
                            <span class="star"><img src="/img/page_modern/star-yellow.svg"></span>
                        </div>
                    </div>
                </div>
                <div class="testimonials-slider">
                    <div class="nxt_btm_area">
                        <div class="nxt_btm_area_left" id="prev">
                            <img src="/img/page_modern/chevron-left-grey.svg">
                        </div>
                        <div class="nxt_btm_area_right" id="next">
                            <img src="/img/page_modern/chevron-right-blue.svg">
                        </div>
                    </div>
                    <div class="about-testimonials_text" id="testimonail-slider">
                        <div class="testimonials_text_area">
                            <div class="testimonials-left">
                                <div class="autor-testimonial">
                                    <p class="name">Customer 1</p>
                                    <p class="location">Company 1</p>
                                </div>
                                <p class="testimonail-text">
                                    Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi.
                                </p>
                            </div>
                            <div class="testimonials-right">
                                <div class="autor-testimonial">
                                    <p class="name">Customer 2</p>
                                    <p class="location">Company 2</p>
                                </div>
                                <p class="testimonail-text">
                                    Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet. Quisque rutrum. Aenean imperdiet. Etiam ultricies nisi vel augue.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="testimonail-paginator" id="nav"></div>
                </div>
            </div>
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
        <div id="dialog-modal">
            <div id="map_canvas" style="width: 865px; height: 442px;">Google Map</div>
        </div>
        <!-- end container -->
    </div>
</div>
<div class="footer-bg"><?= $footer ?></div>
</body>
</html>
