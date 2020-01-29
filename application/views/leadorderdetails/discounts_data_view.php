<div class="items_content2_bl1">
    <div class="items_content2_bl1_tx">Add’l message to appear on invoice:</div>
    <textarea class="items_textarea input_border_gray" readonly="readonly"><?=$invoice_message?></textarea>
</div>
<div class="items_content2_bl2">
    <input type="text" class="items_input1 input_border_gray" readonly="readonly" placeholder="Misc Charge:" value="<?= $mischrg_label1 ?>"/>
    <input type="text" class="items_input1 input_border_gray" readonly="readonly" placeholder="Misc Charge:" value="<?= $mischrg_label2 ?>"/>
    <div class="items_content2_bl2_tx">Discount:</div>
    <input type="text" class="items_input2 input_border_gray" readonly="readonly" placeholder="Courtesy Discount" value="<?= $discount_label ?>"/>
</div>
<div class="items_content2_bl3">
    <div class="discountdescript <?=(empty($discount_descript) ? 'empty_icon_file' : 'icon_file')?>" data-content="<?=$discount_descript?>" style="margin: 41px 2px 0 5px;">
        &nbsp;
    </div>
    <input type="text" class="items_input3 input_border_gray input_text_right" readonly="readonly" value="<?=MoneyOutput($mischrg_val1) ?>"/>
    <input type="text" class="items_input3 input_border_gray input_text_right" readonly="readonly" value="<?=MoneyOutput($mischrg_val2) ?>"/>
    <?php if ($discount_val>0) { ?>
        <input type="text" class="items_discount input_border_gray input_text_right" readonly="readonly" value="(<?=MoneyOutput($discount_val) ?>)"/>
    <?php } else { ?>
        <input type="text" class="items_input3 input_border_gray input_text_right" readonly="readonly" value="<?=MoneyOutput($discount_val) ?>"/>
    <?php } ?>    
</div>
