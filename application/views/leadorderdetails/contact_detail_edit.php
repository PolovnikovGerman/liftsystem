<?php foreach ($data as $row) { ?>
        <div class="contact_content_line">
        <div class="contact_content_name">
            <input type="text" class="contact_name_input contact_input input_border_gray ordecontactinput" data-contact="<?=$row['order_contact_id']?>" data-field="contact_name" value="<?=$row['contact_name']?>"/>
        </div>
        <div class="contact_content_phone">
            <input type="text" class="contact_phone_input contact_input input_border_gray ordecontactinput" data-contact="<?=$row['order_contact_id']?>" data-field="contact_phone" value="<?=$row['contact_phone']?>"/>
        </div>
        <div class="contact_content_email">
            <input type="text" class="contact_email_input contact_input input_border_gray ordecontactinput" data-contact="<?=$row['order_contact_id']?>" data-field="contact_emal" value="<?=$row['contact_emal']?>"/>
            <div class="contactemail_clone" data-contactid="<?=$row['order_contact_id']?>">
                <i class="fa fa-clone" aria-hidden="true"></i>
            </div>
        </div>
        <div class="contact_content_art contact_art_input">
            <input type="checkbox" class="input_checkbox ordecontactchk" <?=(ValidEmail($row['contact_emal']) ? '' : 'disabled="disabled"')?> data-contact="<?=$row['order_contact_id']?>" data-field="contact_art" <?=($row['contact_art']==1 ? 'checked="ckecked"' : '')?>/>
        </div>
        <div class="contact_content_inv contact_inv_input">
            <input type="checkbox" class="input_checkbox ordecontactchk" <?=(ValidEmail($row['contact_emal']) ? '' : 'disabled="disabled"')?> data-contact="<?=$row['order_contact_id']?>" data-field="contact_inv" <?=($row['contact_inv']==1 ? 'checked="ckecked"' : '')?>/>
        </div>
        <div class="contact_content_trk contact_trk_input">
            <input type="checkbox" class="input_checkbox ordecontactchk" <?=(ValidEmail($row['contact_emal']) ? '' : 'disabled="disabled"')?> data-contact="<?=$row['order_contact_id']?>" data-field="contact_trk" <?=($row['contact_trk']==1 ? 'checked="ckecked"' : '')?> />
        </div>
    </div>
<?php } ?>
