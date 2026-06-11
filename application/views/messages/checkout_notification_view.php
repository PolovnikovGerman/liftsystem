<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>LIFT 2025 - Payment confirmemail</title>
    <style type="text/css">

        body {
            font-family: Arial;
        }
        .paybodyemail {
            /*width: 1024px;*/
            width: 740px;
            margin: 0 auto;
            padding: 1% 3%;
        }
        .logocompany {
            display: block;
            width: 50%;
            margin: 0 25%;
            padding: 20px 0;
        }
        .titleemail {
            /*font-size: 107px;*/
            font-size: 80px;
            font-weight: bold;
            color: #000169;
            text-align: center;
            width: 100%;
        }
        .subtitleemail {
            font-size: 28px;
            font-weight: bold;
            color: #0608b4;
            text-align: center;
            width: 100%;
        }
        .emailbody {
            margin-top: 30px;
            width: 100%;
        }
        .emailbody-name {
            width: 100%;
            font-weight: bold;
            font-size: 30px;
            margin-bottom: 20px;
        }
        .emailbody-text p {
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 24px;
            line-height: 36px;
        }
        .payconfirmed-box {
            background: #f3f9f3;
            border: 1px solid #c9e1cc;
            border-radius: 10px;
            padding: 5% 7%;
            margin-top: 30px;
        }
        .payconfirmed-title {
            width: 100%;
            text-align: center;
            font-weight: bold;
            color: #096a0a;
            font-size: 40px;
            margin-bottom: 20px;
        }
        .payconfboxes {
            width: 100%;
            display: flex;
            justify-content: center;
        }
        .payconf-box {
            width: 32%;
            height: 120px;
            border-right: 1px solid #dceddd;
        }
        .payconf-box:last-child {
            border-right: 1px solid #f3f9f3;
            /*position: relative;*/
            /*top: 12px;*/
        }
        .payconfbox-title {
            width: 100%;
            text-align: center;
            font-size: 20px;
            margin-bottom: 20px;
        }
        .payconf-amountpaid {
            width: 100%;
            text-align: center;
            color: #11540e;
            font-weight: bold;
            font-size: 30px;
            line-height: 60px;
        }
        .payconf-paymethod {
            width: 100%;
            text-align: center;
            font-size: 30px;
            line-height: 60px;
        }
        .paymethod-card {
            color: #0d1086;
            font-weight: bold;
        }
        .payconf-transactdate {
            width: 100%;
            text-align: center;
            font-size: 24px;
            line-height: 30px;
        }
        .emailbody-end {
            width: 100%;
            margin-top: 30px;
            margin-bottom: 30px;
        }
        .emailbody-endtitle {
            width: 100%;
            text-align: center;
            font-weight: bold;
            font-size: 28px;
            color: #090b43;
            margin-bottom: 10px;
        }
        .emailbody-endtext {
            width: 100%;
            text-align: center;
            font-size: 21px;
        }
        .emailfooter {
            width: 100%;
            border-top: 2px solid #c9cad3;
            padding-top: 20px;
            display: flex;
            justify-content: center;
        }
        .emailfooter-icon {
            display: inline-block;
            width: 20%;
        }
        .emailfooter-body {
            display: inline-block;
            width: 78%;
        }
        .emailfooter-iconbox {
            background: #eaecfd;
            border: 1px solid #d6d9fd;
            width: 78px;
            height: 78px;
            line-height: 78px;
            border-radius: 39px;
            color: #0201d6;
            text-align: center;
            font-weight: bold;
            font-size: 50px;
            margin: 15px auto;
        }
        .emailfooter-body h4 {
            font-size: 22px;
            margin-top: 0;
            margin-bottom: 5px;
        }
        .emailfooter-body p {
            font-size: 18px;
            line-height: 26px;
            margin-top: 0;
            margin-bottom: 0;
        }
        .emailfooter-contacts {
            width: 100%;
        }
        .emailfooter-email {
            display: inline-block;
            font-size: 18px;
            color: #1f2ee7;
            padding-right: 15px;
            border-right: 1px solid #374177;
        }
        .emailfooter-phone {
            display: inline-block;
            font-size: 18px;
            color: #1f2ee7;
            padding-left: 15px;
        }
    </style>
<!--    <link rel="stylesheet" href="style-confirmemail.css?v=1.01">-->
</head>
<body>
<div class="paybodyemail">
    <div class="logocompany">
        <img src="<?=base_url()?>img/messages/logo-stressballs.svg" alt="Logo">
    </div>
    <div class="titleemail">Thank You!</div>
    <div class="subtitleemail">Your Payment Has Been Confirmed</div>
    <div class="emailbody">
        <div class="emailbody-name">Hi Jane,</div>
        <div class="emailbody-text">
            <p>Thank you for your payment! Your payment of $3,305.00 has been
                successfully processed and your order is now confirmed.</p>
            <p>We appreciate your business and look forward to  delivering a great product!</p>
        </div>
        <div class="payconfirmed-box">
            <div class="payconfirmed-title">PAYMENT CONFIRMED</div>
            <div class="payconfboxes">
                <div class="payconf-box">
                    <div class="payconfbox-title">Amount Paid</div>
                    <div class="payconf-amountpaid">$3,305.00</div>
                </div>
                <div class="payconf-box">
                    <div class="payconfbox-title">Payment Method</div>
                    <div class="payconf-paymethod"><span class="paymethod-card">VISA</span> - 3928</div>
                </div>
                <div class="payconf-box">
                    <div class="payconfbox-title">Transaction Date</div>
                    <div class="payconf-transactdate">
                        <div class="transactdate-date">May 25, 2026</div>
                        <div class="transactdate-time">10:24 AM EST</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="emailbody-end">
            <div class="emailbody-endtitle">Thank you for your business.</div>
            <div class="emailbody-endtext">We're excited to be working with you.</div>
        </div>
    </div>
    <div class="emailfooter">
        <div class="emailfooter-icon">
            <div class="emailfooter-iconbox">?</div>
        </div>
        <div class="emailfooter-body">
            <h4>Questions?</h4>
            <p>If you have any questions regarding your order, reply to this email or contact us at:</p>
            <div class="emailfooter-contacts">
                <div class="emailfooter-email">sales@stressballs.com</div>
                <div class="emailfooter-phone">800-790-6090</div>
            </div>
        </div>
    </div>
</div>
</body>



</html>