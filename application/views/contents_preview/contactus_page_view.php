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
    <link rel="stylesheet" type="text/css" href="/css/contents_preview/contactus.css?r=<?= getsitecssversion() ?>"/>
    <script type="text/javascript" src="/js/jquery.min.js"></script>
    <script type="text/javascript" src="/js/jquery-ui.min.js"></script>
</head>
<body>
<header><?= $header ?></header>
<div class="main-content">
    <div class="container">
        <!-- content -->
        <div class="body-site contact-page">
            <h3 class="title-page"><?=$contents['contact_maintitle']?></h3>
            <p class="subtitle"><?=$contents['contact_subtitle']?></p>
            <p><?=$contents['contact_smalltext']?></p>
            <p class="time-work"><?=$contents['contact_bluetext']?></p>
            <div class="contact-body">
                <div class="phone" style="height: 183px;">
                    <div class="title-box">By Telephone:</div>
                    <p class="main-phone"><?=$address['address_phone']?></p>
                    <p class="local-phone"><?=$address['address_phonelocal']?> (Local)</p>
                </div>
                <div class="email" style="height: 183px;">
                    <div class="title-box">By Email:</div>
                    <p><a href="mailto:<?=$address['address_email']?>"><?=$address['address_email']?></a></p>
                </div>
                <div class="mail"  style="height: 193px;">
                    <div class="title-box">By Mail:</div>
                    <p><?=$address['address_visit']?></p>
                </div>
                <div class="chat"  style="height: 193px;">
                    <div class="title-box">By Live Chat:</div>
                    <p>
                        <a href="http://messenger.providesupport.com/messenger/1ksls0kzbgwwk1sj20g2zyuqfs.html" target="_blank" onclick="return false;" class="chat">
                            Click here
                        </a>
                        to chat</br>by Instant Message</p>
                </div>
                <div class="questions"  style="height: 180px;">
                    <div class="title-box">Common Questions:</div>
                    <p><a href="/faq.html" class="link">Click here</a> to</br>view our FAQ</p>
                </div>
                <div class="hours"  style="height: 180px;">
                    <div class="title-box">Our Hours:</div>
                    <p><?=$address['address_hours']?></br><?=$address['address_days']?></p>
                </div>
                <div class="contack-email-form">
                    <form id="myquestion">
                        <h3>Submit Our Email Form</h3>
                        <div class="input-group-left">
                            <input type="text" placeholder="Enter your name" id="sendername" name="sendername">
                            <input type="text" placeholder="Enter your email" id="senderemail" name="senderemail">
                            <input type="text" placeholder="Enter your phone" id="senderphone" name="senderphone">
                            <select name="sendersubjct" id="sendersubjct">
                                <option value="Question">Question</option>
                                <option value="Comment">Comment</option>
                                <option value="Media">Media</option>
                                <option value="Suggestion">Suggestion</option>
                                <option value="Complaint">Complaint</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="input-group-right">
                            <textarea placeholder="Enter message here" id="sendertxt" name="sendertxt"></textarea>
                            <div class="radio-group">
                                I prefer
                                <label> <input type="radio" name="communicate" value="email" checked="checked"> email </label>
                                <label> <input type="radio" name="communicate" value="phone"> phone </label></div>
                            <div class="captcha">
                                <span><?=$math_captcha_question;?></span>
                                <input type="text" name="math_captcha" id="math_captcha" pattern="[0-9]"/>
                            </div>
                            <div class="bnt-send" id="sendquestion">Send</div>
                        </div>
                    </form>
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
        <!-- end content -->
    </div>
</div>
<div class="footer-bg"><?= $footer ?></div>
</body>
</html>
