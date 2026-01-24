<div class="topgreyblock">
    <div class="proofrequsts-row">
        <div class="proofrequsts-group">
            <label>Order:</label>
            <?php if (empty($order_num)) : ?>
            <div class="pr-orderboxconnect">Connect</div>
            <?php else : ?>
            <div class="pr-orderbox"><?=$order_num?></div>
            <?php endif; ?>
        </div>
        <div class="proofrequsts-group prgroup-request">
            <label>Request:</label>
            <div class="pr-requestbox"><?=($proof_num=='' ? '&nbsp;' : ($brand=='SR' ? 'rp' : 'pr').$proof_num)?></div>
        </div>
    </div>
</div>
<div class="prgreyblock-left">
    <div class="proofrequsts-row">
        <div class="proofrequsts-group prgroup-customer">
            <label>Customer:</label>
            <input type="text" name="customer_name" class="proofreqcommon" data-field="customer" value="<?=$customer?>"/>
        </div>
    </div>
    <div class="proofrequsts-row">
        <div class="proofrequsts-group prgroup-contact">
            <label>Contact:</label>
            <input type="text" name="customer_contact" class="proofreqcommon" data-field="customer_contact" value="<?=$customer_contact?>"/>
        </div>
    </div>
    <div class="proofrequsts-row">
        <div class="proofrequsts-group prgroup-email">
            <label>Email:</label>
            <input type="text" name="customer_email" class="proofreqcommon" data-field="customer_email" value="<?=$customer_email?>">
        </div>
    </div>
    <div class="proofrequsts-row">
        <div class="proofrequsts-group prgroup-phone">
            <label>Phone:</label>
            <input type="text" name="customer_phone" class="proofreqcommon" data-field="customer_phone" value="<?=$customer_phone?>">
        </div>
    </div>
</div>
<div class="prgreyblock-right">
    <div class="proofrequsts-row">
        <div class="proofrequsts-group prgroup-itemnumber">
            <label>Item:</label>
            <input type="text" name="item_number" readonly="readonly" value="<?=$item_number?>"/>
        </div>
    </div>
    <div class="proofrequsts-row">
        <div class="proofrequsts-group prgroup-item">
            <select class="proofreqcommon" data-field="item_id">
                <option value="">Select Item</option>
                <?php foreach ($items_list as $row) { ?>
                    <option value="<?=$row['item_id']?>" <?=($row['item_id']==$item_id ? 'selected="selected"' : '')?>><?=$row['item_name']?></option>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="proofrequsts-row">
        <div class="proofrequsts-group prgroup-notes">
            <label>Notes:</label>
            <textarea class="proofreqcommon" data-field="artwork_note"><?=$artwork_note?></textarea>
        </div>
    </div>
</div>
