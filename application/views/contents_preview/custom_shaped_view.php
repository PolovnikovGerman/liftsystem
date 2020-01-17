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
    <link rel="stylesheet" type="text/css" href="/css/contents_preview/custom_shaped.css?r=<?= getsitecssversion() ?>"/>
    <script type="text/javascript" src="/js/contents_preview/jquery.min.js"></script>
    <script type="text/javascript" src="/js/contents_preview/jquery-ui.min.js"></script>
    <script type="text/javascript" src="/js/cycle2/jquery.cycle2.min.js"></script>
</head>
<body>
<header><?=$header?></header>
<div class="main-content">
    <div class="container">
        <div class="body-site custom-shaped-page">
            <div class="hero-banner">
                <img src="<?=$contents['custom_mainimage']?>">
                <div class="title-banner">
                    <h3><?= $contents['custom_maintitle']?></h3>
                    <h4><?= $contents['custom_secondarytext'] ?></h4>
                </div>
                <div class="button-banner">
                    <button class="scrollstartnow">Get Started Today<span><img src="/img/page_modern/chevron-down.svg"></span>
                    </button>
                </div>
            </div>
            <div class="intro">
                <h3><?= $contents['custom_belowimagetext']?></h3>
                <ul>
                    <li><?= $contents['custom_bulletpoint1'] ?></li>
                    <li><?= $contents['custom_bulletpoint2'] ?></li>
                    <li><?= $contents['custom_bulletpoint3'] ?></li>
                    <li><?= $contents['custom_bulletpoint4'] ?></li>
                    <li><?= $contents['custom_bulletpoint5'] ?></li>
                </ul>
                <p><?=$contents['custom_longerbodytext'] ?></p>
            </div>
            <!-- Finish -->
            <h4 class="question"><?=$contents['custom_abovegallerytext']?></h4>

            <div class="categories">
                <div class="categories-title">
                    <h3><?=$contents['custom_gallerytitle']?></h3>
                </div>
                <div class="categories-boxes">
                    <?php $numr=0;?>
                    <?php foreach ($galleries as $row) { ?>
                    <?php if ($row['gallery_show']==1) { ?>
                    <?php if ($numr==0) { ?>
                    <div class="row-boxes">
                        <?php } ?>
                        <div class="box-category" data-category="<?=$row['custom_gallery_id']?>">
                            <h3><?=$row['gallery_name']?></h3>
                            <!-- insert images -->
                            <div class="slider-heroes cycle-slideshow" data-category="<?=$row['custom_gallery_id']?> data-cycle-fx="scrollHorz"
                            data-cycle-pause-on-hover="true"
                            data-cycle-speed="600"
                            data-cycle-manualSpeed="200"
                            data-slides = "> div"
                            data-pager = "#examplepager_<?=$row['custom_gallery_id']?>"
                            >
                            <?php $numi=0;?>
                            <?php foreach($row['items'] as $image) { ?>
                                <?php if ($numi==0) { ?>
                                    <div class="row-images">
                                <?php } ?>
                                <div class="img-box">
                                    <img class="icon-image" src="<?=$image['item_source']?>" data-fullimg="<?=$image['item_source']?>">
                                    <img class="icon-zoom" src="/img/page_modern/icon-zoom.png" data-fullimg="<?=$image['item_source']?>">
                                </div>
                                <?php $numi++;?>
                                <?php if ($numi==3) { ?>
                                    </div>
                                    <?php $numi=0;?>
                                <?php } ?>
                            <?php } ?>
                        </div>
                        <?php if ($numi>0) { ?>
                    </div>
                <?php } ?>
                    <!-- end images -->
                    <div class="example_view_manager">
                        <div class="paginator_area">
                            <div class="prevexample" id='prevexample_<?=$row['custom_gallery_id']?>'><img src="/img/page_modern/cp-chevron-left.svg"></div>
                            <div class="circle-paginator cycle-pager" id="examplepager_<?=$row['custom_gallery_id']?>"></div>
                            <div class="nextexample" id='nextexample_<?=$row['custom_gallery_id']?>'><img src="/img/page_modern/cp-chevron-right.svg"></div>
                        </div>
                    </div>
                </div>
                <?php } ?>
                <?php $numr++;?>
                <?php if ($numr==2) { ?>
                <!-- close row-boxes -->
            </div>
            <?php $numr=0;?>
            <?php } ?>
            <?php } ?>
            <?php if ($numr>0) { ?>
            <!-- close row-boxes -->
        </div>
        <?php } ?>
    </div>
</div>
<h5 class="services"><?=$contents['custom_belowgallerrytext'] ?>
    <br>
    <?=$contents['custom_belowgallerrysubtext'] ?>
</h5>

<div class="contact-call">
    <p>Call Today for More Information</p>
    <p class="phone">1-800-790-6090</p>
    <p>or</p>
    <div class="button">
        <a href="#startnow" onclick="return false;" class="scrollstartnow bnt-start">Get Started Today</a>
    </div>
</div>

<div class="process-block">
    <div class="title-process">
        <h3><?=$contents['custom_stepprocesstitle']?></h3>
    </div>
    <ul>
        <li>
            <img src="/img/page_modern/process-step-1.png">
            <h4>Send Us<br>Your Idea</h4>
        </li>
        <li>
            <img src="/img/page_modern/process-step-2.png">
            <h4>Your Idea is<br>Brought to Life.</h4>
        </li>
        <li>
            <img src="/img/page_modern/process-step-3.png">
            <h4>Approve a<br>3D Clay Model</h4>
        </li>
        <li>
            <img src="/img/page_modern/process-step-4.png">
            <h4>See Final<br>Preview Picture</h4>
        </li>
        <li>
            <img src="/img/page_modern/process-step-5.png">
            <h4>Recieve Your<br>Custom Stressball</h4>
        </li>
    </ul>
    <p><?=$contents['custom_belowstepstext'] ?></p>
</div>

<div class="reviews-case_studies">
    <div class="reviews-block">
        <div class="google-rating-reviews">
            <img class="google-logo" src="/img/page_modern/Google_2015_logo.svg">
            <span class="rating">4.8</span>
            <div class="stars">
                <span class="star"><img src="/img/page_modern/star-blue.svg"></span>
                <span class="star"><img src="/img/page_modern/star-blue.svg"></span>
                <span class="star"><img src="/img/page_modern/star-blue.svg"></span>
                <span class="star"><img src="/img/page_modern/star-blue.svg"></span>
                <span class="star"><img src="/img/page_modern/star-blue.svg"></span>
            </div>
        </div>
        <div class="review-box">
            <div id="customreview">
                <div class="review_area">
                    <div class="autor-review">
                        <p class="name">John Waters</p>
                        <p class="location">Good Company LLC</p>
                    </div>
                    <p class="review-text">
                        Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor.
                        Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.
                        Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim.
                        Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu.
                    </p>
                </div>
            </div>
        </div>
        <div class="example_view_manager">
            <div class="paginator_area">
                <div class="prevexample" id='prevreview'><img src="/img/page_modern/cp-chevron-left.svg">
                </div>
                <div class="circle-paginator cycle-pager" id="reviewpager"></div>
                <div class="nextexample" id='nextreview'><img src="/img/page_modern/cp-chevron-right.svg">
                </div>
            </div>
        </div>
    </div>
    <div class="case_studies-block">
        <div class="case_studies-title">
            <h3>Case Studies</h3>
            <div class="pagination-cs">
                <div id="customstudy-pager" class="pagination"></div>
            </div>
        </div>
        <!-- Slider -->
        <div id="casestudyarea" style="float: left; width: 100%">
            <div class="casestudy_slider_area">
                <div class="casestudy_image">
                    <img src="<?=$case_study[0]['casestudy_image']?>" alt="Case Study"/>
                </div>
                <div class="case_studies-body">
                    <h4><?=$case_study[0]['casestudy_title']?></h4>
                    <p><?=$case_study[0]['casestudy_text']?></p>
                    <?php if (!empty($case_study[0]['casestudy_expand'])) { ?>
                        <div class="case_studies-expand"><?=$case_study[0]['casestudy_expand']?></div>
                        <span class="casestudyreadmore"">READ MORE</span>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="banner-contact">
    <div class="contact-info">
        <a name="startnow">
            <p>Call Us Today for a Quote</p>
        </a>
        <h3>1-800-790-6090</h3>
        <p>or fill out our easy form</p>
    </div>
    <div class="banner-orange">
        <p>Current Special Offer</p>
        <h4>FREE MOLDS!</h4>
        <p class="price">$500 Value</p>
    </div>
</div>
<div class="custom-form">
    <h3>Get a Quote for Your Custom Shape</h3>
    <p class="title">We respond the same or next business day</p>
    <div class="block-left">
        <input type="hidden" id="customquoteupload" value=""/>
        <input type="hidden" id="customquotesources" value=""/>
        <div class="input-group">
            <input type="text" class="form-control cf-name" placeholder="Name" id="customer_name" autocomplete="new-password"/>
            <input type="text" class="form-control cf-company" placeholder="Company" id="customer_company" autocomplete="new-password"/>
        </div>
        <div class="input-group">
            <input type="text" class="form-control cf-phone" placeholder="Phone" id="customer_phone" autocomplete="new-password"/>
        </div>
        <div class="input-group">
            <input type="text" class="form-control cf-email" placeholder="Email" id="customer_email" autocomplete="new-password"/>
        </div>
        <h4>Shipping Address:</h4>
        <div class="input-group">
            <select class="form-control cf-country" id="ship_country">
                <option value="US">United States</option>
            </select>
            <input type="text" class="form-control cf-zipcode" placeholder="Zip code" id="ship_zipcode"
                   value="" autocomplete="new-password"/>
        </div>
        <div class="input-group" id="shipcitystate">
            <input type="text" class="form-control cf-city"
                   placeholder="City" value="" id="ship_city" autocomplete="new-password"/>
            <div id="shipstate" style="display: none;">
            </div>
        </div>
        <h4>Quantity Desired:</h4>
        <div class="input-group">
            <p class="cf-quantity-info">Orders must be a minimum of 1000.<br>Unlimited maximum.</p>
            <select class="form-control cf-quantity" id="quota_qty">
                <option value="1000-2000">1000-2000</option>
                <option value="2000-3000">2000-3000</option>
                <option value="3000-5000">3000-5000</option>
                <option value="5000-7500">5000-7500</option>
                <option value="7500-10000">7,500-10,000</option>
                <option value="10000-20000">10,000-20,000</option>
                <option value="20000+">20,000 +</option>
            </select>
        </div>
        <h4>Need by Date</h4>
        <div class="input-group">
            <p class="cf-date-info">Standard production is 5-6 weeks. Rush<br>production may be possible. Please
                call.<br>Longer production available with special <br>discounting. Please inquire.</p>
            <div class="input-date">
                <input type="text" class="form-control" id="ship_date" autocomplete="new-password">
            </div>
        </div>
    </div>
    <div class="block-right">
        <h4>Description of Shape:</h4>
        <div class="input-group">
                            <textarea type="text" class="cf-info" placeholder="Please describe your shape"
                                      id="shape_desription"></textarea>
        </div>
        <h4>Additional Notes:</h4>
        <div class="input-group">
                            <textarea type="text" class="cf-notes" placeholder="List any other helpful info"
                                      id="shape_notes"></textarea>
        </div>
        <h4>Please attach pictures (only if have):</h4>
        <p class="information">We accept PDF, Word, Excel, JPG, PNG, EPS, AI and PSD files.<br>5MB max file size</p>
        <div id="attachment_area"></div>
        <div class="input-group">
            <button class="bnt-add" id="addattachment">+ Add Attachment</button>
        </div>
    </div>
    <div class="button-form">
        <div class="requestquote">Request Quote</div>
    </div>
</div>
<!-- Custom Form -->
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
<div class="footer-bg"><?=$footer?></div>
</body>
</html>
