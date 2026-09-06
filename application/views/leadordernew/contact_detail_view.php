<?php foreach ($contacts as $contact) : ?>
    <div class="tblconts_tr">
        <div class="tblconts_td tblconts_name">
            <div class="tblconts_inptbox truncateoverflowtext"><?=$contact['contact_name']?></div>
        </div>
        <div class="tblconts_td tblconts_phone">
            <div class="tblconts_inptbox truncateoverflowtext"><?=$contact['contact_phone']?></div>
        </div>
        <div class="tblconts_td tblconts_email">
            <div class="tblconts_inptbox truncateoverflowtext"><?=$contact['contact_phone']?></div>
        </div>
        <div class="tblconts_td tblconts_boxes">
            <div class="tblconts_checkbox">
                <input type="checkbox" name="contact_art" class="" <?=$contact['contact_art']==1 ? "checked" : ""?> disabled/>
            </div>
        </div>
        <div class="tblconts_td tblconts_boxes">
            <div class="tblconts_checkbox">
                <input type="checkbox" name="contact_inv" class="" <?=$contact['contact_inv']==1 ? "checked" : ""?> disabled/>
            </div>
        </div>
        <div class="tblconts_td tblconts_boxes">
            <div class="tblconts_checkbox">
                <input type="checkbox" name="contact_trk" class="" <?=$contact['contact_trk']==1 ? "checked" : ""?> disabled/>
            </div>
        </div>
    </div>
<?php endforeach; ?>
