<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Checkout Invitation</title>
<!--    <link rel="stylesheet" href="style-paymentemail.css?v=1.01">-->
    <style type="text/css">

        body {
            font-family: Arial;
        }
        a {
            text-decoration: none;
        }
        .paybodyemail {
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
            font-size: 36px;
            font-weight: bold;
            color: #000169;
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
            font-size: 24px;
            margin-bottom: 15px;
        }
        .emailbody-text p {
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 18px;
            line-height: 30px;
        }
        .orderdetailsbox {
            border: 1px solid #dfe3fd;
            border-radius: 10px;
            width: 90%;
            padding: 4% 5%;
        }
        .orderdetailsbox-title {
            font-weight: bold;
            color: #00004e;
            font-size: 26px;
            margin-bottom: 20px;
        }
        .orderline {
            width: 100%;
            border-bottom: 1px solid #dedfe0;
            display: flex;
            justify-content: space-between;
            padding-bottom: 15px;
            padding-top: 10px;
        }
        .orderline-title {
            display: inline-flex;
            color: #02024f;
            font-size: 20px;
            line-height: 24px;
        }
        .orderline-item {
            display: inline-flex;
            font-weight: bold;
            font-size: 22px;
            line-height: 24px;
            padding-left: 15px;
            text-align: right;
        }
        .ordertotalline {
            width: 100%;
            display: flex;
            justify-content: space-between;
            padding-bottom: 10px;
            padding-top: 25px;
        }
        .ordertotal-title {
            display: inline-flex;
            font-size: 20px;
            line-height: 24px;
        }
        .ordertotal-price {
            display: inline-flex;
            font-size: 20px;
            line-height: 24px;
        }
        .amountreceivedline {
            width: 100%;
            display: flex;
            justify-content: space-between;
            padding-bottom: 25px;
            padding-top: 10px;
            border-bottom: 1px solid #dedfe0;
        }
        .amountreceived-title {
            display: inline-flex;
            font-size: 20px;
            line-height: 24px;
        }
        .amountreceived-price {
            display: inline-flex;
            font-size: 20px;
            line-height: 24px;
            color: #fe0b0c;
        }
        .balanceline {
            width: 100%;
            display: flex;
            justify-content: space-between;
            padding-top: 18px;
        }
        .balanceline-title {
            display: inline-flex;
            font-size: 26px;
            line-height: 24px;
            color: #070865;
            font-weight: bold;
        }
        .balanceline-price {
            display: inline-flex;
            font-size: 26px;
            line-height: 24px;
            font-weight: bold;
        }
        .secureblock {
            border: 1px solid #ced6fc;
            background: #f4f6fe;
            border-radius: 10px;
            width: 100%;
            margin-top: 20px;
        }
        .secureblock-title {
            width: 100%;
            text-align: center;
            color: #0a0a7c;
            font-weight: bold;
            font-size: 26px;
        }
        .secureblock-text {
            width: 100%;
            text-align: center;
            font-size: 22px;
            margin-top: 7px;
            margin-bottom: 14px;
        }
        .secureblock-button {
            width: 60%;
            margin: 0 20%;
            border-radius: 7px;
            background: #000173;
            color: #fff;
            font-weight: bold;
            font-size: 26px;
            line-height: 56px;
            text-align: center;
        }
        .secureblock-greytext {
            width: 100%;
            text-align: center;
            font-size: 18px;
            color: #55596e;
            margin-top: 14px;
            margin-bottom: 14px;
        }
        .secureblock-icon {
            width: 100%;
            margin-top: 10px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .secureblock-iconbox {
            border: 1px solid #00017e;
            width: 40px;
            height: 40px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .secureblock-iconbox img {
            width: 24px;
            height: auto;
        }
        .paymentcards {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            margin-top: 20px;
        }
        .paymentcards img {
            height: 40px;
            width: auto;
        }


        .emailfooter {
            width: 100%;
            border: 1px solid #dce0fc;
            border-radius: 10px;
            display: flex;
            justify-content: center;
            padding-top: 10px;
            padding-bottom: 10px;
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
</head>
<body>
<div class="paybodyemail">
    <div class="logocompany">
        <?php if ($brand=='SR') : ?>
            <img src="<?=base_url()?>img/messages/logo-stressrelievers.svg" alt="Logo"/>
        <?php else: ?>
            <img src="<?=base_url()?>img/messages/logo-stressballs.svg" alt="Logo"/>
        <?php endif; ?>
    </div>
    <div class="titleemail">Secure Online Payment</div>
    <div class="emailbody">
        <div class="emailbody-name">Hi <?=ucfirst($name)?>,</div>
        <div class="emailbody-text">
            <p>Thank you for your order! We're prepared your payment page and your balance is ready to be paid securely online.</p>
        </div>
        <div class="orderdetailsbox">
            <div class="orderdetailsbox-title">ORDER DETAILS</div>
            <div class="orderline">
                <div class="orderline-title">Order:</div>
                <div class="orderline-item"><?=$itemname?></div>
            </div>
            <div class="ordertotalline">
                <div class="ordertotal-title">Order Total:</div>
                <div class="ordertotal-price"><?=MoneyOutput($revenue)?></div>
            </div>
            <?php if ($paid > 0) : ?>
            <div class="amountreceivedline">
                <div class="amountreceived-title">Amount Received:</div>
                <div class="amountreceived-price">(<?=MoneyOutput($paid)?>)</div>
            </div>
            <?php endif; ?>
            <div class="balanceline">
                <div class="balanceline-title">BALANCE DUE:</div>
                <div class="balanceline-price"><?=MoneyOutput($balance)?></div>
            </div>
        </div>
    </div>
    <div class="secureblock">
        <div class="secureblock-icon">
            <div class="secureblock-iconbox">
                <img src="<?=base_url()?>img/messages/lock-icon.svg" alt="Lock"/>
            </div>
        </div>
        <div class="secureblock-title">SECURE ONLINE PAYMENT</div>
        <div class="secureblock-text">Click the button to make your payment:</div>
        <a href="http://sb.local/payment/Psgerzs13Gs"><div class="secureblock-button">PAY <?=$balance?> SECURELY</div></a>
        <div class="secureblock-greytext">This link is unique to you and your order.</div>
    </div>
    <div class="paymentcards">
        <img src="<?=base_url()?>img/messages/payment-cards.svg" alt="Payment Cards"/>
    </div>
    <div class="emailfooter">
        <div class="emailfooter-icon">
            <div class="emailfooter-iconbox">?</div>
        </div>
        <div class="emailfooter-body">
            <h4>Questions?</h4>
            <p>If you have any questions regarding your order, reply to this email or contact us at:</p>
            <div class="emailfooter-contacts">
                <?php if ($brand=='SR') : ?>
                    <a href="mailto:sales@@stressrelievers.com"><div class="emailfooter-email">sales@stressrelievers.com</div></a>
                <?php else : ?>
                    <a href="mailto:sales@@stressballs.com"><div class="emailfooter-email">sales@stressballs.com</div></a>
                <?php endif; ?>
                <div class="emailfooter-phone">800-790-6090</div>
            </div>
        </div>
    </div>
</div>
</body>



</html>