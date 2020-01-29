<div class="items_content2_bl1">
    <div class="items_content2_bl1_tx">Add’l message to appear on invoice:</div>
    <textarea class="items_textarea input_border_gray inputleadorddata" data-field="invoice_message" data-entity="order"><?=$invoice_message?></textarea>
</div>
<div class="itemsdiscountmiscarea">
    <div class="itemsdiscountmiscrow">
        <div class="itemsdiscountmisclabel">
            <input type="text" class="items_input1 input_border_gray inputleadorddata" data-field="mischrg_label1" data-entity="order" placeholder="Misc Charge:" value="<?= $mischrg_label1 ?>"/>
        </div>
        <div class="itemsdiscountmiscvalue">
            <input type="text" class="items_input3 input_border_gray input_text_right inputleadorddata" data-field="mischrg_val1" data-entity="order" value="<?=number_format($mischrg_val1,2)?>"/>
        </div>
    </div>
    <div class="itemsdiscountmiscrow">
        <div class="itemsdiscountmisclabel">
            <input type="text" class="items_input1 input_border_gray inputleadorddata" data-field="mischrg_label2" data-entity="order" placeholder="Misc Charge:" value="<?=$mischrg_label2 ?>"/>
        </div>
        <div class="itemsdiscountmiscvalue">
            <input type="text" class="items_input3 input_border_gray input_text_right inputleadorddata" data-field="mischrg_val2" data-entity="order" value="<?=  number_format($mischrg_val2,2)?>"/>
        </div>
    </div>
    <div class="itemsdiscountmiscrow">
        <div class="items_content2_bl2_tx">Discount:</div>
        <div class="itemdiscountlabel">
            <input type="text" class="items_input2 input_border_gray inputleadorddata" data-field="discount_label" data-entity="order" placeholder="Courtesy Discount" value="<?=$discount_label?>"/>
        </div>
        <div class="itemdiscounticon">
            <div class="discountdescript <?=(empty($discount_descript) ? 'empty_icon_file' : 'icon_file')?> active">&nbsp;</div>
        </div>
        <div class="itemdiscountvalue">
            <input type="text" class="items_input3 input_border_gray input_text_right inputleadorddata" data-field="discount_val" data-entity="order" value="<?=  number_format($discount_val,2)?>"/>
        </div>
    </div>
</div>
