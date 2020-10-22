<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>America’s Stress Ball Source</title>
        <style>
/*
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
*/
/*
    Created on : Feb 8, 2016, 11:32:14 PM
    Author     : polovnikov-go
*/

* { margin: 0px; padding: 0px; }
html, body { font-family: arial; font-size: 12px; color: #414141;  }
input, select, textarea { font-family: arial; font-size: 13px; color: #868686; font-style:italic;  }
body { background:#fff; width: 750px; }
img { border: 0px; }
a, input { outline: none; }
/* ### Global Classes ### */
/* All Document */
.content {
    float: left;
    padding-left: 15px;
    padding-top: 40px;
    width: 740px;
}
.logo {
    background: url('/img/invoice/invoice_logo_new.png') no-repeat scroll left top transparent;
    float: left;
    width: 409px;
    height: 76px;
}
.invoicenum {
    background: url('/img/invoice/invoice_num.png') no-repeat scroll left top transparent;
    float: left;
    height: 63px;
    width: 320px;
}
.numberinv {
    width: 137px;
    height: 43px;
    line-height: 43px;
    color: #0000FF;
    font-size: 22px;
    font-weight: bold;
    text-align: center;
}
td.ouraddress {
    width: 409px;
}
td.adresrow {
    text-align: left;
    font-size: 16px;
    height: 22px;
}
td.adresrow > span {
    color: #0000FF;
}
td.invoicedatelabel {
    height: 28px;
    vertical-align: middle;
    font-size: 18px;
    text-align: left;
    width: 102px;
}
td.invoicedate {
    height: 28px;
    vertical-align: middle;
    font-size: 18px;
    text-align: left;
    width: 83px;
}
td.customercode {
    background: url('/img/invoice/customer_code_bg.png') no-repeat scroll left top transparent;
    width: 266px;
    height: 37px;
}
td.customercodevalue {
    height: 22px;
    text-align: center;
    font-size: 18px;
    font-weight: bold;
    width: 90px;
}
td.terms {
    width: 163px;
    height: 22px;
    font-size: 16px;
    text-align: center;
    vertical-align: middle;
}
td.terms.header {
    background: url('/img/invoice/terms_head_bg.png') no-repeat scroll left top transparent;
    height: 35px;
}
td.paymentdue {
    width: 163px;
    height: 22px;
    font-size: 16px;
    text-align: center;
    vertical-align: middle;
}
td.paymentdue.header {
    background: url('/img/invoice/paymentdue_head_bg.png') no-repeat scroll left top transparent;
    height: 35px;
}
td.shidate {
    width: 163px;
    height: 22px;
    font-size: 16px;
    text-align: center;
    vertical-align: middle;
}
td.shidate.header {
    background: url('/img/invoice/shipdate_head_bg.png') no-repeat scroll left top transparent;
    height: 35px;
}
td.deliverydate {
    width: 163px;
    height: 22px;
    font-size: 16px;
    text-align: center;
    vertical-align: middle;
}
td.deliverydate.header {
    background: url('/img/invoice/deliverydate_head_bg.png') no-repeat scroll left top transparent;
    height: 35px;
}
td.billingadr {
    width: 340px;
    font-size: 16px;
    text-align: left;
    vertical-align: middle;
}
td.billingadr.header {
    background: url('/img/invoice/billto_head_bg.png') no-repeat scroll left top transparent;
    height: 35px;
}
td.shipingadr {
    width: 340px;
    font-size: 16px;
    text-align: left;
    vertical-align: middle;
}
td.shipingadr.header {
    background: url('/img/invoice/shipto_head_bg.png') no-repeat scroll left top transparent;
    height: 35px;
}
td.addresrow {
    text-align: left;
    font-size: 14px;
    overflow: hidden;
    white-space: nowrap;
    width: 340px;
    color: #000000;
    height: 16px;
    vertical-align: middle;
}
/* details */
tr.invoicedetails {
}
tr.invoicedetails.grey {
    background-color: #e1e1e1;
}
tr.invoicedetails > td {
    height: 38px;
    font-size: 16px;
    color: #000000;
}
td.itemnum {
    text-align: center;
    width: 116px;
    vertical-align: middle;
}
td.itemnum.header {
    background: url('/img/invoice/itemnum_head_bg.png') no-repeat scroll left top transparent;
    height: 33px;
}
td.itemdescript {
    text-align: left;
    width: 321px;
    vertical-align: middle;
}
td.itemdescript.header {
    background: url('/img/invoice/itemdescript_head_bg.png') no-repeat scroll left top transparent;
    height: 33px;
}
td.itemqty {
    text-align: center;
    width: 58px;
    vertical-align: middle;
}
td.itemqty.header {
    background: url('/img/invoice/itemqty_head_bg.png') no-repeat scroll left top transparent;
    height: 33px;
}
td.itemprice {
    text-align: center;
    width: 116px;
    vertical-align: middle;
}
td.itemprice.header {
    background: url('/img/invoice/priceeach_head_bg.png') no-repeat scroll left top transparent;
    height: 33px;
}
td.itemsubtotal {
    text-align: center;
    width: 116px;
    vertical-align: middle;
}
td.itemsubtotal.header {
    background: url('/img/invoice/subtotal_head_bg.png') no-repeat scroll left top transparent;
    height: 33px;
}
/* Bottom */
td.advpicplace {
    width: 417px;
    height: 144px;
    font-size: 18px;
}
td.invoicetotals {
    /* background: url('/img/invoice/totals_bg.png') no-repeat scroll left top transparent; */
    /* height: 141px; */
    border: 2px solid #000000;
    border-radius: 5px;
    width: 329px;
}
td.taxlabel {
    padding-left: 6px;
    height: 24px;
    font-size: 18px;
    color: #000000;
    width: 212px;
}
td.taxvalue {
    font-size: 18px;
    color: #000000;
    width: 111px;
}
td.totallabel {
    padding-left: 6px;
    height: 26px;
    font-size: 19px;
    font-weight: bold;
    color: #000000;
    width: 212px;
}
td.totalvalue {
    font-size: 19px;
    font-weight: bold;
    color: #0000FF;
}
tr.payments {
    /* background-color: #000000; */
    background-color: #e1e1e1;
}
td.paymentlabel {
    padding-left: 6px;
    height: 24px;
    font-size: 18px;
    /* color: #FFFFFF; */
    color: #000000;
    width: 212px;
}
td.paymentvalue {
    height: 24px;
    font-size: 18px;
    /* color: #FFFFFF; */
    color: #000000;
    width: 99%;
}
        </style>
    </head>
    <body>
        <div class="content">
            <table cellpadding="0" style="width:750px; padding: 0;">
                <tr>
                    <td style="width: 394px;">
                        <!-- <div class="logo">&nbsp;</div> -->
                        <img src="<?=$_SERVER['DOCUMENT_ROOT']?>/img/invoice/invoice_logo_bluetrack-stressballs.jpg" alt="Logo" style="width: 100%"/>
                    </td>
                    <td style="width: 15px">&nbsp;</td>
                    <td>
                        <div class="invoicenum">
                            <table>
                                <tr>
                                    <td style="width: 178px;">&nbsp;</td>
                                    <td style="vertical-align: central;"><div class="numberinv"><?=$order_num?></div></td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>
            </table>
            <table style="width: 750px;">
                <tr>
                    <td class="ouraddress">
                        <table>
                            <tr><td class="adresrow">855 Bloomfield Ave</td></tr>
                            <tr><td class="adresrow">Clifton, NJ 07012</td></tr>
                            <tr><td class="adresrow">Call Us at <span>1-800-790-6090</span></td></tr>
                            <tr><td class="adresrow"><span>www.bluetrack.com</span></td></tr>
                        </table>
                    </td>
                    <td>
                        <table style="margin-left: 100px;">
                            <tr>
                                <td class="invoicedatelabel">Invoice Date:</td>
                                <td class="invoicedate"><?=$order_date?></td>
                            </tr>
                        </table>
                        <?php if (!empty($customer_code)) { ?>
                            <table style="margin-left: 42px;">
                                <tr>
                                    <td class="customercode">
                                        <table>
                                            <tr>
                                                <td style='width: 164px;'>&nbsp;</td>
                                                <td class="customercodevalue"><?=$customer_code?></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        <?php } ?>
                    </td>
                </tr>
            </table>
            <table style="width: 750px;">
                <tr>
                    <td class="terms header">&nbsp;</td>
                    <td style="width: 13px">&nbsp;</td>
                    <td class="paymentdue header">&nbsp;</td>
                    <td style="width: 71px;">&nbsp;</td>
                    <td class="shidate header">&nbsp;</td>
                    <td style="width: 13px">&nbsp;</td>
                    <td class="deliverydate header">&nbsp;</td>
                </tr>
                <tr>
                    <td class="terms">
                        <?=$terms?>
                    </td>
                    <td style="width: 13px;">&nbsp;</td>
                    <td class="paymentdue">
                        <?=$payment_due?>
                    </td>
                    <td style="width: 71px;">&nbsp;</td>
                    <td class="shidate"><?=$shipdate?></td>
                    <td style="width: 13px">&nbsp;</td>
                    <td class="deliverydate"><?=$arrive?></td>
                </tr>
            </table>
            <div style="height: 7px;">&nbsp;</div>
            <table style="width: 750px;">
                <tr>
                    <td class="billingadr header">&nbsp;</td>
                    <td style="width: 71px;">&nbsp;</td>
                    <td class="shipingadr header">&nbsp;</td>
                </tr>
                <tr>
                    <td class="billingadr">
                        <table>
                            <?php foreach ($billing as $row) { ?>
                            <tr>
                                <td class="addresrow"><?=$row?></td>
                            </tr>
                            <?php } ?>
                        </table>
                    </td>
                    <td style="width: 71px;">&nbsp;</td>
                    <td class="shipingadr">
                        <table>
                            <?php foreach ($shipping as $row) { ?>
                            <tr>
                                <td class="addresrow"><?=$row?></td>
                            </tr>
                            <?php } ?>
                        </table>
                    </td>
                </tr>
            </table>
            <table style="width: 750px; margin-top: 15px;" cellspacing="0">
                <tr>
                    <td class="itemnum header">&nbsp;</td>
                    <td class="itemdescript header">&nbsp;</td>
                    <td class="itemqty header">&nbsp;</td>
                    <td class="itemprice header">&nbsp;</td>
                    <td class="itemsubtotal header">&nbsp;</td>
                </tr>
                <?php $numpp=0;?>
                <?php foreach ($details as $row) { ?>
                <tr class="invoicedetails <?=$numpp%2==0 ? 'grey' : ''?>">
                    <td class="itemnum"><?=$row['item_num']?></td>
                    <td class="itemdescript"><?=$row['item_description']?></td>
                    <td class="itemqty"><?=$row['item_qty']?></td>
                    <td class="itemprice" style="color: <?=$row['item_color']?>"><?=$row['item_price']?></td>
                    <td class="itemsubtotal" style="color: <?=$row['item_color']?>"><?=$row['item_subtotal']?></td>
                </tr>
                <?php $numpp++;?>
                <?php } ?>
            </table>
            <table style="width: 750px; margin-top: 15px;">
                <tr>
                    <!-- Place for Adds -->
                    <td class="advpicplace">
                        <?=$invoice_message?>
                    </td>

                    <td class="invoicetotals">
                        <table cellspacing="0">
                            <tr>
                                <td class="taxlabel">NJ <?=$tax_term?>% Sales Tax (0.0%)</td>
                                <td class="taxvalue"><?=$tax?></td>
                            </tr>
                            <tr>
                                <td class="totallabel">Total</td>
                                <td class="totalvalue"><?=$total?></td>
                            </tr>
                            <?php if ($payments_count) { ?>
                                <?php foreach ($payments_detail as $row) { ?>
                                    <tr class="payments">
                                        <td class="paymentlabel"><?=$row['label']?></td>
                                        <td class="paymentvalue"><?=$row['value']?></td>
                                    </tr>
                                <?php } ?>
                            <?php } ?>
                            <tr>
                                <td class="totallabel">Balance Due</td>
                                <td class="totalvalue"><?=$balance?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>
