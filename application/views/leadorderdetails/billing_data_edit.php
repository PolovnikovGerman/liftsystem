<div class="billing_content1">
    <div class="billing_content1_line">
        <div class="billing_customerpotitle">PO#</div>
        <input type="text" class="billing_input3 input_border_gray billinginput leftalign" data-field="customer_ponum" placeholder="CuPo" value="<?=$billing['customer_ponum']?>">
    </div>
    <div class="billing_content1_line">
        <input type="text" class="billing_input1 input_border_gray billinginput leftalign" data-field="customer_name" placeholder="First and Last Name" value="<?=$billing['customer_name']?>"/>
    </div>
    <div class="billing_content1_line">
        <input type="text" class="billing_input2 input_border_gray billinginput leftalign" data-field="company" placeholder="Company" value="<?=$billing['company']?>"/>
    </div>
    <div class="billing_content1_line">
        <input type="text" class="billing_input4 input_border_gray billinginput leftalign" id="billorder_line1" data-field="address_1" placeholder="Address 1" value="<?=$billing['address_1']?>"/>
        <input type="text" class="billing_input4 input_border_gray billinginput leftalign" data-field="address_2" placeholder="Address 2" value="<?=$billing['address_2']?>"/>
    </div>
    <div class="billing_content1_line">
        <input type="text" class="billing_input5 input_border_gray billinginput leftalign" data-field="city" placeholder="City" value="<?=$billing['city']?>"/>
        <div id="billingstateselectarea" class="billingstateselectarea">
            <?php if (count($states)==0) { ?>
                &nbsp;
            <?php } else { ?>
                <select class="billing_select1 input_border_gray">
                    <option value="" <?=$billing['state_id']=='' ? 'selected="selected"' : '' ?>>&nbsp;</option>
                    <?php foreach ($states as $row) {?>
                        <option value="<?=$row['state_id']?>" <?=$row['state_id']==$billing['state_id'] ? 'selected="selected"' : ''?>><?=$row['state_code']?></option>
                    <?php } ?>
                </select>                
            <?php } ?>            
        </div>
        <input type="text" class="billing_input6 input_border_gray billinginput leftalign" data-field="zip" placeholder="Zip" value="<?=$billing['zip']?>"/>
        <select class="billing_select2 input_border_gray" data-field="country_id">
            <?php foreach ($countries as $row) { ?>
                <option value="<?=$row['country_id']?>" <?=$row['country_id']==$billing['country_id'] ? 'selected="selected"' : ''?>><?=$row['country_name']?></option>
            <?php } ?>            
        </select>
        <input type="hidden" id="billordercntcode" value="<?=$country_code?>"/>
    </div>
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