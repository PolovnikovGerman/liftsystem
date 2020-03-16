<div class="artpopup_commonrow">
    <div class="artpopup_customerlabel">Customer:</div>
    <div class="artpopup_customerval">
        <input type="text" class="order_customer" name="customer_name" id="customer_name" value="<?=$customer?>"/>
    </div>
    <div class="artpopup_contactlabel">Contact:</div>
    <div class="artpopup_contactval">
        <input type="text" class="order_contact" name="order_contact" id="order_contact" value="<?=$customer_contact?>"/>
    </div>
    <div class="artpopup_phonelabel">Phone:</div>
    <div class="artpopup_phoneval">
        <input type="text" class="order_phone" name="order_phone" id="order_phone" value="<?=$customer_phone?>"/>
    </div>
</div>
<div class="artpopup_commonrow merged">
    <div class="artpopup_common_merged">
        <div class="artpopup_emaillabel">Email:</div>
        <div class="artpopup_emailval">
            <input type="text" class="order_email" name="customer_email" id="customer_email" value="<?=$customer_email?>"/>
        </div>
        <div class="artpopup_itemlabel">Item:</div>
        <div class="artpopup_itemval">
            <select class="order_itemname" name="item_id" id="order_item_id">
                <option value="">Select Item</option>
                <?php foreach ($items_list as $row) { ?>
                    <option value="<?=$row['item_id']?>" <?=($row['item_id']==$item_id ? 'selected="selected"' : '')?>><?=$row['item_name']?></option>
                <?php } ?>
            </select>
        </div>
        <div class="artpopup_itemlabel">Item #</div>
        <div class="artpopup_itemnumval">
            <input type="text" class="order_itemnum" readonly="readonly" name="order_itemnum" id="order_itemnum" value="<?=$item_number?>"/>
        </div>
        <div class="artpopup_otheritemarea">
            <div class="artpopup_itemlabel"><?=$other_item_label?></div>
            <div class="artpopup_otheritemvalue">
                <input type="text" class="order_otheritemdat" name="other_item" id="other_item" value="<?=$other_item?>"/>
            </div>
        </div>
    </div>
    <div class="artpopup_notelabel">Notes:</div>
    <div class="artpopup_noteval">
        <textarea class="order_notes" id="order_notes" name="order_notes"><?=$artwork_note?></textarea>
    </div>
</div>