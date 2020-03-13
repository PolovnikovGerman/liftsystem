<div id="leftbillingdataarea">
    <?=$leftbilling?>
</div>
<div class="billing_content2">
    <div class="billing_content2_line">
        <input type="checkbox" name="name3" value="c1" class="input_checkbox">
        <div class="billing_content2_bl2 opast">
            <div class="b_content2_bl2_tx text_gray">Adjustment</div>
            <div class="icon_file" style="margin: 0px 0 0 3px;">&nbsp;</div>
            <input type="text" class="billing_input7 input_border_gray" />
        </div>
    </div>
    <div class="billing_content2_line">
        <div class="billing_content2_bl1 <?=$order['invoice_class']?>">
            <div class="b_content2_bl1_txt <?= ($order['invoice_class']=='active' ? 'text_white' : 'text_gray') ?>">INV</div>
        </div>
        <div class="block_8 text_white">
            <div class="block_8_backgr1">&nbsp;</div>
            <div class="block_8_backgr2 block_8_text">
                <div class="block_8_text1">TOTAL:</div>
                <div class="block_8_text3 text_bold" id="ordertotaloutput"><?=MoneyOutput($order['revenue'])?></div>
            </div>
            <div class="block_8_backgr3">&nbsp;</div>
        </div>
    </div>
    <!--  opast -->
    <div class="billing_content2_line2">
        <?php if ($financeview==1) { ?>
        <div class="billing_content2_bl3 text_blue newpaymentadd">+ new manual payment / refund</div>        
        <?php }  else { ?>
        &nbsp;
        <?php } ?>
    </div>
</div>