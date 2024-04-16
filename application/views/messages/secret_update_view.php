<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Quote Request</title>
    <style type="text/css">
        html, body, div, span, applet, object, iframe,
        h1, h2, h3, h4, h5, h6, p, blockquote, pre,
        a, abbr, acronym, address, big, cite, code,
        del, dfn, em, font, img, ins, kbd, q, s, samp,
        small, strike, strong, sub, sup, tt, var,
        dl, dt, dd, ol, ul, li,
        fieldset, form, label, legend,
        table, caption, tbody, tfoot, thead, tr, th, td {
            margin: 0;
            padding: 0;
            border: 0;
            outline: 0;
            font-weight: inherit;
            font-style: inherit;
            font-size: 100%;
            font-family: inherit;
            vertical-align: baseline;
        }
        a img, :link img, :visited img {
            border: 0;
        }
        body {
            line-height: 1;
            color: black;
            background: white;
            font-size: 14px;
            text-align:center; /* for ie5.+*/
        }
        ol, ul {
            list-style: none;
        }
        .main {
            clear: both;
            float: left;
            width: 780px;
            text-align: left;
            margin-left: 20px;
            margin-top: 10px;
        }
        .main p.headmsg {
            clear: both;
            float: left;
            margin-left: 15px;
            margin-bottom: 15px;
            width: 100%;
        }
        .main p.msgmaintxt {
            clear: both;
            float: left;
            text-indent: 15px;
            margin-bottom: 15px;
            line-height: 16px;
            width: 100%;
        }
        .companysignature {
            clear: both;
            float: left;
            width: 75%;
        }
        .companysignature .companysignature_row {
            clear: both;
            float: left;
            margin-top: 3px;
            width: 95%;
        }

    </style>
</head>
<body>
<div class="main">
    <p class="headmsg">Hi, <?=$user_name?></p>
    <p class="msgmaintxt">
        To verify your sign in to system, use secret code <b><?=$secret?></b>
    </p>
    <p class="msgmaintxt">
        For quick add code, use <a href="<?=$url?>">QR code link</a>
    </p>
    <p class="msgmaintxt">
        To install needed software, please use next <a href="<<?=$manual_url?>">manual</a>
        or open <?=$manual_url?> in browser
    </p>
    <div class="companysignature">
        <div class="companysignature_row">Admin Group</div>
        <div class="companysignature_row">BLUETRACK, Inc.</div>
        <div class="companysignature_row">1-800-790-6090</div>
    </div>
</div>
</body>
</html>
