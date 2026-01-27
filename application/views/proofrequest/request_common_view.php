<div class="topgreyblock">
    <div class="proofrequsts-row">
        <div class="proofrequsts-group">
            <label>Order:</label>
            <?php if (empty($artwork['order_num'])) : ?>
            <div class="pr-orderboxconnect">Connect</div>
            <?php else : ?>
            <div class="pr-orderbox"><?=$artwork['order_num']?></div>
            <?php endif; ?>
        </div>
        <div class="proofrequsts-group prgroup-request">
            <label>Request:</label>
            <div class="pr-requestbox"><?=($artwork['proof_num']=='' ? '&nbsp;' : ($artwork['brand']=='SR' ? 'rp' : 'pr').$artwork['proof_num'])?></div>
        </div>
    </div>
</div>
<div class="prgreyblock-left">
    <div class="proofrequsts-row">
        <div class="proofrequsts-group prgroup-customer">
            <label>Customer:</label>
            <input type="text" name="customer_name" class="proofreqcommon" data-fld="customer" value="<?=$artwork['customer']?>"/>
        </div>
    </div>
    <div class="proofrequsts-row">
        <div class="proofrequsts-group prgroup-contact">
            <label>Contact:</label>
            <input type="text" name="customer_contact" class="proofreqcommon" data-fld="customer_contact" value="<?=$artwork['customer_contact']?>"/>
        </div>
    </div>
    <div class="proofrequsts-row">
        <div class="proofrequsts-group prgroup-email">
            <label>Email:</label>
            <input type="text" name="customer_email" class="proofreqcommon" data-fld="customer_email" value="<?=$artwork['customer_email']?>">
        </div>
    </div>
    <div class="proofrequsts-row">
        <div class="proofrequsts-group prgroup-phone">
            <label>Phone:</label>
            <input type="text" name="customer_phone" class="proofreqcommon" data-fld="customer_phone" value="<?=$artwork['customer_phone']?>">
        </div>
    </div>
</div>
<div class="prgreyblock-right">
    <div class="proofrequsts-row">
        <div class="proofrequsts-group prgroup-item">
            <label>Item:</label>
            <select class="proofreqcommon" data-fld="item_id" id="proofrequestitem">
                <option value="">Select Item</option>
                <?php foreach ($items as $row) { ?>
                    <option value="<?=$row['item_id']?>" <?=($row['item_id']==$artwork['item_id'] ? 'selected="selected"' : '')?>><?=$row['item_name']?></option>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="proofrequsts-row">
        <div class="proofrequsts-group prgroup-itemdescription">
            <input type="text" name="other_item" class="proofreqcommon <?=$artwork['item_id'] < 0 ? 'active' : ''?>" data-fld="other_item" value="<?=$artwork['other_item']?>"/>
        </div>
    </div>
    <div class="proofrequsts-row">
        <div class="proofrequsts-group prgroup-notes">
            <label>Notes:</label>
            <textarea class="proofreqcommon" data-fld="artwork_note"><?=$artwork['artwork_note']?></textarea>
        </div>
    </div>
</div>
