<?php foreach ($contacts as $contact) : ?>
    <div class="tblconts_tr">
        <div class="tblconts_td tblconts_name">
            <?php if ($edit==0) : ?>
            <div class="tblconts_inptbox truncateoverflowtext"><?=$contact['contact_name']?></div>
            <?php else: ?>
                <input type="text" name="contact_name" class="contactdata" data-contact="<?=$contact['order_contact_id']?>"
                       data-fld="contact_name" value="<?=$contact['contact_name']?>"/>
            <?php endif; ?>
        </div>
        <div class="tblconts_td tblconts_phone">
            <?php if ($edit==0) : ?>
            <div class="tblconts_inptbox truncateoverflowtext"><?=$contact['contact_phone']?></div>
            <?php else: ?>
            <input type="text" name="contact_phone" class="contactdata" data-contact="<?=$contact['order_contact_id']?>"
            data-fld="contact_phone" value="<?=$contact['contact_phone']?>"/>
            <?php endif; ?>
        </div>
        <div class="tblconts_td tblconts_email">
            <?php if ($edit==0) : ?>
            <div class="tblconts_inptbox truncateoverflowtext"><?=$contact['contact_emal']?></div>
            <?php else: ?>
            <input type="text" name="contact_emal" class="contactdata" data-contact="<?=$contact['order_contact_id']?>"
            data-fld="contact_emal" value="<?=$contact['contact_emal']?>"/>
            <?php endif; ?>
        </div>
        <div class="tblconts_td tblconts_boxes">
            <div class="tblconts_checkbox">
                <input type="checkbox" name="contact_art" class="contactdatachk" data-contact="<?=$contact['order_contact_id']?>"
                       data-fld="contact_art" <?=$contact['contact_art']==1 ? "checked" : ""?> <?=$edit==0 ? 'disabled' : ''?>/>
            </div>
        </div>
        <div class="tblconts_td tblconts_boxes">
            <div class="tblconts_checkbox">
                <input type="checkbox" name="contact_inv" class="contactdatachk" data-contact="<?=$contact['order_contact_id']?>"
                       data-fld="contact_inv" <?=$contact['contact_inv']==1 ? "checked" : ""?> <?=$edit==0 ? 'disabled' : ''?>/>
            </div>
        </div>
        <div class="tblconts_td tblconts_boxes">
            <div class="tblconts_checkbox">
                <input type="checkbox" name="contact_inv" class="contactdatachk" data-contact="<?=$contact['order_contact_id']?>"
                       data-fld="contact_trk" <?=$contact['contact_trk']==1 ? "checked" : ""?> <?=$edit==0 ? 'disabled' : ''?>/>
            </div>
        </div>
    </div>
<?php endforeach; ?>
