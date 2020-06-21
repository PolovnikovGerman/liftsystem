<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>America’s Stress Ball Source</title>
        <style>
            * { margin: 0px; padding: 0px; }
            html, body { font-family: arial; font-size: 12px; color: #414141;  }
            input, select, textarea { font-family: arial; font-size: 13px; color: #868686; font-style:italic;  }
            body { background:#fff; }
            img { border: 0px; }
            a, input { outline: none; }
            /* ### Global Classes ### */
            .clear { clear: both; height: 0px; }
            /* All Document */
            #mainCntr {
                clear: both;
                float: left;
                width: 800px;
            }
            /* Header */
            #headerenlarged {
                /* background: url("<?=$imgpath?>/quote/quote_head.png") no-repeat scroll left top transparent; */
                background: url("<?=$imgpath?>quote/quote_head.jpg") no-repeat scroll left top transparent;
                clear: both;
                float: left;
                height: 135px;
                margin: 0;
                padding: 0;
                /* padding: 14px 16px;*/
                position: relative;
                width: 800px;
            }
            .headerrow {
                clear : both;
                float: left;
                width: 790px;
            }
            .headerrow_left {
                float: left;
                width: 220px;
                text-align: center;
                padding-top: 15px;
            }
            h1.bluetxt-top {
                color: #FFFFFF;
                font-size: 25px;
                font-weight: bold;
                line-height: 39px;
                text-align: center;
                text-shadow: 0 2px 2px #000000, 0 2px 2px #FFFFFF;
                vertical-align: middle;
            }
            #headerenlarged .inllogo {
                float: left;
                height: 136px;
                padding-top: 17px;
                width: 333px;
            }

            .headerrow_right {
                float: left;
                width: 230px;
                text-align: center;
                padding-top: 15px;
            }

            h1.bluetxt-top1 {
                color: #FFFFFF;
                font-size: 25px;
                font-weight: bold;
                line-height: 39px;
                text-align: center;
                text-shadow: 0 2px 2px #000000, 0 2px 2px #FFFFFF;
                vertical-align: middle;
            }
            /* Center */
            #middilePnt {
                clear: both;
                float: left;
                margin: 0;
                padding: 5px;
                position: relative;
                width: 790px;
            }
            .official-website{
                clear: both;
                float: left;
                width: 780px;
                position:relative;
                padding:0;
                margin:0;
                height:52px
            }
            .datenew1 {
                float: left;
                width : 113px;
                padding: 5px;
                font-size:24px;
                color:#000000;
                text-align:right;
                padding-top: 19px;
            }
            .official-website-center {
                float: left;
                width: 480px;
                font-size:34px;
                font-weight:bold;
                color:#464545;
                line-height:48px;
                text-align:center

            }
            .datenew {
                float: left;
                width: 182px;
                color: #545454;
                font-size: 21px;
                font-style: italic;
                position: relative;
                text-align: left;
            }
            .datenew p {
                display: inline-block;
            }
            .prd-detaits{
                clear: both;
                float:left;
                width:785px;
                border: 2px solid #7c7c7c;
                border-top:none;
                position:relative;
            }
            .qty-area {
                width:100%;
                border:1px solid #6b6bd1;
                height:32px;
                border-left:none;
                float:left;
                border-right:none;
                background:#0000ff
            }
            .qty-area-first{
                width:136px;
                font-size:19px;
                font-weight:bold;
                line-height:32px;
                color:#FFFFFF;
                float:left;
                text-align:center;
                padding:0px 0px 0px 0px;
            }
            .qty-area-item {
                width:340px;
                font-size:19px;
                font-weight:bold;
                line-height:32px;
                color:#FFFFFF;
                float:left;
                text-align:center;
                padding:0px 0px 0px 0px;
            }
            .qty-area-item2 {
                width: 73px;
                font-size:19px;
                font-weight:bold;
                line-height:32px;
                color:#FFFFFF;
                float:left;
                text-align:center;
                padding:0px 0px 0px 0px;
            }
            .qty-area-item3 {
                width:110px;
                font-size:19px;
                font-weight:bold;
                line-height:32px;
                color:#FFFFFF;
                float:left;
                text-align:center;
                padding:0px 0px 0px 0px;
            }
            .qty-area-item4{
                width:114px;
                font-size:19px;
                font-weight:bold;
                line-height:32px;
                color:#FFFFFF;
                float:left;
                text-align:center;
                padding:0px 0px 0px 0px;
            }
            .des-area{
                clear: both;
                float:left;
                padding:0px 0;
                border-bottom: 1px solid #808080;
                width:785px;
            }
            .qty-area-first-new {
                clear: both;
                float:left;
                width:136px;
                color:#FFFFFF;
                text-align:center;
                padding:0px 0px 0px 0px;
            }
            .qty-area-first-new .dtl-bg {
                background:url(<?=$imgpath?>/quote/dtl-bg.jpg) no-repeat;
                width:120px;
                height:115px;
                margin-left:7px;
                padding:3px 4px 4px 3px;
            }
            .qty-area-item-new {
                width:326px;
                float:left;
                padding:0px 0px 0px 10px;
                border-right:#333333 solid 1px
            }
            .qty-area-item-new .itemtitle {
                font-size:22px;
                font-weight:bold;
                color:#464545;
                text-align:left
            }
            .qty-area-item-new ul {
                list-style:none;
                padding:6px 0px 0px 20px
            }
            .qty-area-item-new ul li {
                font-size:20px;
                font-style:italic;
                padding:4px 0px;
                color:#545454;
                line-height:20px
            }

            .qty-area-item2-new{
                width: 73px;
                font-size: 19px;
                float: left;
                padding: 0px 0px 0px 0px;
                border-left: #333 solid 1px;
                border-right: #333 solid 1px;
                margin-left: -1px;
            }
            .itemqty {
                clear: both;
                float: left;
                font-size:24px;
                font-weight:bold;
                color:#000000;
                padding-top:1px;
                text-align: center;
                width: 73px;
                margin-top: 12px;
            }
            .qty-area-item3-new {
                width:110px;
                float:left;
                padding:0px 0px 0px 0px;
                border-left:#333333 solid 1px;
                border-right:#333333 solid 1px;
                margin-left:-1px;
            }
            .itemsaletitle {
                clear: both;
                float: left;
                width: 100%;
                font-size:20px;
                font-weight:bold;
                color:#0000ff;
                /* padding-top:1px; */
                text-align:center;
                /* margin-top: 6px; */
            }
            .itemsalevalue {
                clear: both;
                float: left;
                width: 100%;
                text-align: center;
                font-size:24px;
                font-weight:bold;
                color:#0000ff;
            }
            .oldpriceview {
                clear: both;
                float: left;
                padding: 5px;
                width: 100%;
            }
            .oldprice {
                float:left;
                margin-left:12px;
                height: 53px;
                width: 100%;
                background: url(<?=$imgpath?>quote/save_price_bg.jpg) repeat scroll 0 0 transparent;
            }
            .oldpricetitle {
                clear: both;
                float: left;
                width: 100%;
                color: #C8C8C8;
                font-size: 18px;
                text-align: center;
            }
            .oldpricevalue {
                clear: both;
                float: left;
                width: 100%;
                color: #C8C8C8;
                font-size: 20px;
                font-weight: bold;
                text-align: center;
            }
            .qty-area-item4-new {
                width:112px;
                min-height:134px;
                float:left;
                text-align:center;
                padding:0px 0px 0px 0px;
                border-left:#333333 solid 1px;
                margin-left:-1px;
            }
            .qty-area-item4-new p {
                font-size:22px;
                font-weight:bold;
                color:#2c2c2c;
                padding-top:5px;
            }
            .des-area2 {
                border-bottom: 1px solid #808080;
                float: left;
                clear: both;
                padding: 5px 10px 0 10px;
                position: relative;
                width: 766px;
            }
            .des-area2-left {
                float: left;
                padding: 0;
                position: relative;
                width: 472px;
            }
            .des-area2-left-head {
                float: left;
                width: 472px;
                font-size:18px;
                font-style:italic;
                color:#545454;
                text-align:left;
            }
            .des-area2-left .img-free {
                clear: both;
                float: left;
                padding-top: 5px;
                width: 445px;
            }
            .img-free-graphic {
                float: left;
                width: 224px;
            }
            .bluetxtnew {
                float:left;
                color: #0000FF;
                float: left;
                font-size: 24px;
                font-weight: bold;
                padding-top: 55px;
                text-align: right;
                width: 210px;
            }
            .des-area2-right {
                width:309px;
                float:left;
                padding:0px 0px 0px 0px;
                position:relative;
            }
            .des-area2-right-inner {
                clear: both;
                float: left;
                width: 309px;
            }
            .des-area2-right-inner-left {
                float:left;
                width:200px;
                font-size:18px;
                font-weight:bold;
                color:#646363;
                text-align:right;
            }
            .des-area2-right-inner-right {
                width:102px;
                float:left;
                text-align:right;
                font-size:18px;
                font-weight:bold;
                color:#363636;
            }
            .des-area2-right-inner-left span {
                font-size:20px;
                color:#c8c8c8;
                text-decoration:line-through;
            }
            .des-area2-right-inner-right span {
                font-size:20px;
                color:#c8c8c8;
                text-decoration:line-through;
            }
            .des-area2-right-inner-right span.txt-spn {
                font-size:20px;
                color:#0000ff;
                text-decoration: none;
            }
            .des-area2-right-inner-left span.txt-spn {
                font-size:20px;
                color:#0000ff;
                text-decoration: none;
            }

            /* Form */
            .hidden-fees {
                clear: both;
                float: left;
                margin: 0;
                padding: 0px 0 0 0;
                position: relative;
                width: 780px;
            }
            .hidden-fees-head {
                float: left;
                clear: both;
                width: 735px;
                color: #0E037B;
                font-size: 23px;
                font-weight: bold;
                height: 28px;
                padding: 0 0 0px;
                text-align: center;
            }
            .hidden-fees h1 {
                color: #0E037B;
                font-size: 25px;
                padding: 0 0 10px 10px;
                text-align: left;
            }
            .hidden-fees h1 span {
                color: #4C4C4C;
                font-size: 18px;
                font-weight: normal;
                padding-left: 8px;
            }

            .hidden-form {
                width:750px;
                padding:10px;
                float:left;
                border:#545555 dashed 2px;
                position:relative;
            }
            .hidden-form-first {
                width:240px;
                float:left;
                margin:0;
                padding:0;
            }

            .txtbilling textarea{
                background: url(<?=$imgpath?>quote/text-area-bg-new.jpg) no-repeat;
                width:234px;
                height:149px;
                border:none;
                padding:10px;
                margin-bottom:15px;
                font-style:normal;
                font-size:16px;
            }
            .hidden-form-first textarea {
                background:url(<?=$imgpath?>quote/text-area-bg-new.jpg) no-repeat;
                width:234px;
                height:149px;
                border:none;
                padding:10px;
                margin-bottom:15px;
                font-style:normal;
                font-size:16px;
            }
            .hidden-form-first span {
                color:#474646;
                font-size:22px;
                text-align:left;
                display:block;
                line-height:22px;
                font-weight:bold;
                padding:5px 0px 10px 0px;
            }

            .hidden-form-second {
                margin-left:10px;
                padding:0;
                width: 100px;
            }

            .frm_cnt_area-new {
                width: 250px;
                margin:0;
                padding:0;

            }
            .frm_cnt_area-new-title{
                width:66px;
                text-align:right;
                font-size: 18px;
                font-weight:bold;
                color:#303032;
                padding-right: 10px;
                vertical-align: middle;
            }
            .frm_cnr_area_new-value {
                width:180px;
                padding: 5px;
            }
            .contact-head {
                color:#474646;
                font-size:22px;
                text-align:left;
                display:block;
                line-height:22px;
                font-weight:bold;
                padding:5px 0px 10px 0px;
            }
            .mar {
                color:#474646;
                font-size:22px;
                text-align:left;
                font-weight:bold;
                padding: 0;
                width: 100%;
            }

            .frm_cnt_area-new2{float:left; width:100%; margin:10px 0px 6px 0px; padding:0;}
            .frm_cnt_area-new2 span{float:left; display:block; line-height:38px; text-align:right; font-size: 18px; padding-left:15px; font-weight:bold; color:#303032; }
            .frm_cnt_area-new2 input{background:url(<?=$imgpath?>creadit-card-new.jpg) no-repeat; padding:5px; float: left; width:244px; height:29px; line-height:21px; border:none; margin:0px 0px 0px 10px; font-style:italic}
            .contactinput {
                background:url(<?=$imgpath?>input-name-new.jpg) no-repeat;
                padding:5px;
                float: left;
                width:180px;
                height:29px;
                line-height:21px;
                border:none;
                margin:0px 0px 0px 10px;
                font-style:italic;
            }
            .contactinput-phone {
                background: url(<?=$imgpath?>input-phone.jpg);
                width: 189px;
                float: left;
                height: 38px;
                line-height: 21px;
                border: none;
                margin: 0px 0px 0px 10px;
                font-style: italic;
            }
            .payment_title {
                width:100px;
                text-align: right;
                font-size: 18px;
                font-weight:bold;
                color:#303032;
            }
            .payment_value {
                float:left;
                clear: both;
                width: 253px;

            }
            .payment_input {
                background: url(<?=$imgpath?>creadit-card-new.jpg) no-repeat;
                padding: 5px;
                float: left;
                width: 253px;
                height: 29px;
                line-height: 21px;
                border: none;
                margin: 0px 0px 0px 10px;
                font-style: italic;
            }
            .payment_expdate {
                background:url(<?=$imgpath?>input-date.jpg) no-repeat;
                padding:5px;
                float:left;
                width:95px;
                height:28px;
                line-height:21px;
                border:none;
                margin:0px 0px 0px 0px;
                font-style:italic;
            }
            .frm_cnt_area-new3{float:left; width:90%; margin:10px 0px 6px 0px; padding:0;}
            .frm_cnt_area-new3 span{float:left; width:100px; line-height:38px; text-align:left; font-size: 18px;  padding-left:15px; font-weight:bold; color:#303032; }


            .signature-line{
                width: 366px;
                height: 4px;
                background: #434343;
                float: left;
                margin-left: 255px;
            }

            .hidden-form-third{width:210px; float:left; margin-left:5px; padding:0;}

            .txtcomments {
                background:url(<?=$imgpath?>input-comments-new.jpg) no-repeat;
                width:192px;
                height:139px;
                border:none;
                padding:10px;
                margin-bottom:15px;
                font-style:normal;
                font-size:16px;
            }
            .hidden-form-third span{ color:#474646; font-size:22px; text-align:left; display:block; line-height:22px; font-weight:bold; padding:5px 0px 10px 0px}
            .hidden-form-third h4{ color:#474646; font-size:22px; text-align:left; display:block; line-height:22px; font-weight:bold; margin-top:10px; padding:5px 0px 10px 0px}
            .hidden-fees_h3 {
                color: #0400A1;
                font-size: 25px;
                font-style: italic;
                font-weight: bold;
                text-align: center;
            }
            /* Footer */
            td.details-footer {
                background-color: #1A1AFF;
                border-top: 2px solid #9292FF;
                clear: both;
                float: left;
                /* margin: 0 0 0 7px; */
                /* padding:13px 0px 13px 0px; */
                text-align: center;
                width: 790px;
                height: 50px;
                /* TXT */
                font-size: 20px;
                font-weight:bold;
                color:#fff;
                vertical-align:middle;

            }
            .footer_txt {
                float: left;
                width: 100%;
                font-size: 20px;
                font-weight:bold;
                color:#fff;
                text-align:center;
                vertical-align:middle;

            }

            @media print {
                .table {
                    border: #000 solid 1px;
                }

                .table .table_header td {
                    border: #000 solid 1px;
                }

                .table .table_rows td {
                    border: #000 solid 1px;
                }

                .page-break  {
                    display:block;
                    page-break-before:always;
                }
            }
        </style>
    </head>
    <body>
        <!--Begin Wrapper-->
        <div id="wrapper">
            <!--Begin Main Container-->
            <!--<div style="clear: both; height: 21px;">&nbsp;</div> -->
            <div id="mainCntr">
                <!--Begin Header Container-->
                <div id="headerenlarged">
                    <div class="headerrow">
                        &nbsp;
                    </div>
                </div>
                <div style="clear:both;"></div>
                <table style="width:780px;">
                    <tr>
                        <td class="datenew">
                            <span>Over 1200 Stress</span><br/><span>Shapes Available!</span>
                        </td>
                        <td class="official-website-center">Official Website Quote</td>
                        <td class="datenew1"><?=$email_date_show?></td>
                    </tr>
                </table>
                <table style="width: 790px;" cellspacing="0" cellpadding="0">
                    <tr class="qty-area">
                        <td class="qty-area-first">Image:</td>
                        <td class="qty-area-item">Item:</td>
                        <td class="qty-area-item2">Qty:</td>
                        <td class="qty-area-item3">Price Each:</td>
                        <td class="qty-area-item4">Subtotal:</td>
                    </tr>
                    <tr class="des-area">
                        <td class="qty-area-first-new" style="border-bottom: 1px solid #808080;border-left: 1px solid #808080;">
                            <div class="dtl-bg">
                                <?php if ($mainimg=='') {?>
                                    &nbsp;
                                <?php } else { ?>
                                    <img src="<?=$itemimgpath?><?=$mainimg?>" style="width: 120px; height: 115px" alt="Image" />
                                <?php } ?>
                            </div>
                        </td>
                        <td class="qty-area-item-new" style="border-bottom: 1px solid #808080;">
                            <div class="itemtitle"><?= $email_item_name ?></div>
                            <ul>
                                <?php if ($item_number!='') {?>
                                    <li>- Item # <?=$item_number?></li>
                                <?php } ?>
                                <!-- Item colors -->
                                <li>- Color:  <?= $colors ?></li>
                                <!-- Imprint option -->
                                <li>- <?= $colorprint ?></li>
                                <!-- Rush value rush day -->
                                <li>- <?= ($rush == 0 ? '' : '$' . $rush) ?> <?= $rush_days ?> Production</li>
                            </ul>
                        </td>
                        <td class="qty-area-item2-new" style="border-bottom: 1px solid #808080;">
                            <div class="itemqty"><?=QTYOutput($email_qty,0)?></div>
                        </td>
                        <td class="qty-area-item3-new" style="border-bottom: 1px solid #808080;">
                            <table style="width: 100%; margin-top: -20px;">
                                <tr>
                                    <td class="itemsaletitle">Sale:</td>
                                </tr>
                                <tr>
                                    <td class="itemsalevalue"><?=MoneyOutput($saleprice)?></td>
                                </tr>
                                <?php if ($price!=0) {?>
                                    <tr class="oldpriceview">
                                       <td class="oldprice">
                                           <table style="border: none; width: 100%;">
                                               <tr>
                                                   <td class="oldpricetitle">Price</td>
                                               </tr>
                                               <tr>
                                                   <td class="oldpricevalue"><?=MoneyOutput($price)?></td>
                                               </tr>
                                           </table>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </table>
                        </td>
                        <td class="qty-area-item4-new" style="border-bottom: 1px solid #808080;border-right:  1px solid #808080;">
                            <p>$<?=number_format($itemcost,2,'.','')?></p>
                        </td>
                    </tr>
                </table>
                <table style="width: 790px;border:1px solid solid #808080;" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="des-area2-left" style="vertical-align:top;">
                            <table>
                                <tr>
                                    <td colspan="2" class="des-area2-left-head">
                                        Lowest price guaranteed. If you find it for less we will beat it.
                                    </td>
                                </tr>
                                <tr>
                                    <td class="img-free-graphic">
                                        <img src="<?=$imgpath?>quote/larger.jpg" alt="larger" />
                                    </td>
                                    <td class="bluetxtnew" style="height: 90px; vertical-align: bottom;">
                                        <?=($saved==0 ? '&nbsp;' : 'You Save $'.number_format($saved,2,'.','').'!')?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td class="des-area2-right">
                            <table>
                                <tr class="des-area2-right-inner">
                                    <td class="des-area2-right-inner-left">Rush Option:</td>
                                    <td class="des-area2-right-inner-right"><?=number_format($rush,2,'.','')?></td>
                                </tr>
                                <tr class="des-area2-right-inner">
                                    <td class="des-area2-right-inner-left">Imprints/Setup:</td>
                                    <td class="des-area2-right-inner-right"><?=number_format(($setup+$imprint),2,'.','')?></td>
                                </tr>
                                <tr class="des-area2-right-inner">
                                    <td class="des-area2-right-inner-left">NJ Sales Tax:</td>
                                    <td class="des-area2-right-inner-right"><?=number_format($tax,2,'.','')?></td>
                                </tr>
                                <?=$shipinfo?>
                                <tr class="des-area2-right-inner">
                                    <td class="des-area2-right-inner-left"><span>Regular Price:</span></td>
                                    <td class="des-area2-right-inner-right"><span>$<?=number_format($total+$saved, 2, '.','')?></span></td>
                                </tr>
                                <tr class="des-area2-right-inner">
                                    <td class="des-area2-right-inner-left"><span class="txt-spn">Current Price</span></td>
                                    <td class="des-area2-right-inner-right"><span class="txt-spn">$<?=number_format($total, 2, '.','')?></span></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <table>
                    <tr>
                        <td class="hidden-fees-head">Best Price Guaranteed.  No Hidden Fees.</td>
                    </tr>
                    <tr>
                        <td class="quick_order_form" style="color: #0E037B; font-size: 25px; padding: 0 0 10px 10px; text-align: left;">
                            Quick Order Form:
                            <span style="color: #4C4C4C;font-size: 18px;font-weight: normal;padding-left: 8px;padding-top: 7px;">All imprinted orders receive a free proof to approve prior to printing.</span>
                        </td>
                    </tr>
                </table>
                <table style="width: 790px; border: 1px dashed #000000">
                    <tr>
                        <td style="width:10px;">&nbsp;</td>
                        <td class="hidden-form-first"><div class="mar">Billing Address:</div></td>
                        <td class="hidden-form-second"><div class="mar">Contact Info:</div></td>
                        <td class="hidden-form-third"><div class="mar">Comments:</div></td>
                    </tr>
                    <tr>
                        <td style="width:10px;">&nbsp;</td>
                        <td class="hidden-form-first">
                            <!-- <textarea name="billaddress" class="txtbilling">Text Area</textarea> -->
                            <img src="<?=$imgpath?>quote/text-area-bg-new.jpg" />
                        </td>
                        <td class="hidden-form-second">
                            <table>
                                <tr class="frm_cnt_area-new">
                                    <td class="frm_cnt_area-new-title">Name:</td>
                                    <td class="frm_cnr_area_new-value">
                                        <img src="<?=$imgpath?>quote/input-name-new.jpg"/>
                                    </td>
                                </tr>
                                <tr class="frm_cnt_area-new">
                                    <td class="frm_cnt_area-new-title">Email:</td>
                                    <td class="frm_cnr_area_new-value">
                                        <img src="<?=$imgpath?>quote/input-name-new.jpg"/>
                                    </td>
                                </tr>
                                <tr class="frm_cnt_area-new">
                                    <td class="frm_cnt_area-new-title">Phone:</td>
                                    <td class="frm_cnr_area_new-value">
                                        <img src="<?=$imgpath?>quote/input-name-new.jpg"/>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td class="hidden-form-third">
                            <!-- <textarea name="billaddress" class="txtbilling">Text Area</textarea> -->
                            <img src="<?=$imgpath?>quote/input-comments-new.jpg" />
                        </td>
                    </tr>
                    <tr>
                        <td style="width:10px;">&nbsp;</td>
                        <td class="hidden-form-first"><div class="mar">Shipping Address:</div></td>
                        <td class="hidden-form-second"><div class="mar">Payment Info:</div></td>
                        <td class="hidden-form-third"><div class="mar">Optional:</div></td>
                    </tr>
                    <tr>
                        <td style="width:10px;">&nbsp;</td>
                        <td class="hidden-form-first">
                            <img src="<?=$imgpath?>quote/text-area-bg-new.jpg" />
                        </td>
                        <td class="hidden-form-second">
                            <table>
                                <tr>
                                    <td class="frm_cnt_area-new-title" style="width:103px;text-align: center;" colspan="2">Credit Card #</td>
                                </tr>
                                <tr>
                                    <td class="payment_value" colspan="2">
                                        <!--<input name="customercard" type="text" class="payment_input" />-->
                                        <img src="<?=$imgpath?>quote/creadit-card-new.jpg"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="frm_cnt_area-new-title" style="width:132px;">Exp. Date:</td>
                                    <td class="frm_cnr_area_new-value" style="width: 127px;">
                                        <img src="<?=$imgpath?>quote/input-date.jpg"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="payment_title" colspan="2">
                                        <table>
                                            <tr>
                                                <td style="width:75px; text-align: right; font-size: 18px; font-weight:bold; color:#303032;">Signature:</td>
                                                <td style="width: 10px;">&nbsp;</td>
                                                <td style="width:155px; text-align: left; vertical-align: bottom"><hr/></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td class="hidden-form-third" style="vertical-align:top;">
                            <table>
                                <tr>
                                    <td class="frm_cnt_area-new-title">P.O. #</td>
                                    <td class="frm_cnr_area_new-value" style="width: 127px;">
                                        <img src="<?=$imgpath?>quote/input-date.jpg"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <img src="<?=$imgpath?>/quote/bbb_logo_quote.jpg" alt="bbb_logo"  style="margin-left:25px;" />
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>
                </table>
                <table>
                    <tr>
                        <td class="hidden-fees_h3" style="padding-left: 40px;padding-right: 40px;">Order Online, by calling 1-800-790-6090, or by returning this form by fax to 201-604-2688 or by email to sales@bluetrack.com.</td>
                    </tr>
                </table>
                <table>
                    <tr>
                        <td class="details-footer">BLUETRACK, Inc. - 855 Bloomfield Ave - Clifton, NJ 07012 - USA</td>
                    </tr>
                </table>
            </div>
            <!--End Main Container-->
        </div>
        <!--End Wrapper-->
    </body>
</html>