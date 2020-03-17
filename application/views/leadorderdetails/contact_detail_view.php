<?php foreach ($data as $row) { ?>
        <div class="contact_content_line">
        <div class="contact_content_name">
            <input type="text" class="contact_name_input contact_input input_border_gray" value="<?=$row['contact_name']?>" readonly="readonly"/>
        </div>
        <div class="contact_content_phone">
            <input type="text" class="contact_phone_input contact_input input_border_gray" value="<?=$row['contact_phone']?>"  readonly="readonly"/>
        </div>
        <div class="contact_content_email">
            <input type="text" class="contact_email_input contact_input input_border_gray" value="<?=$row['contact_emal']?>" readonly="readonly"/>
        </div>
        <div class="contact_content_art contact_art_input">
            <input type="checkbox" class="input_checkbox" <?=($row['contact_art']==1 ? 'checked="ckecked"' : '')?> disabled="disabled"/>
        </div>
        <div class="contact_content_inv contact_inv_input">
            <input type="checkbox" class="input_checkbox" <?=($row['contact_inv']==1 ? 'checked="ckecked"' : '')?> disabled="disabled"/>
        </div>
        <div class="contact_content_trk contact_trk_input">
            <input type="checkbox" class="input_checkbox" <?=($row['contact_trk']==1 ? 'checked="ckecked"' : '')?> disabled="disabled"/>
        </div>
    </div>
<?php } ?>
