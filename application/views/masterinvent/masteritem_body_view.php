<div class="inventoryitem_body_content">
    <div class="datarow">
        <div class="itemdatalabel">Item Name:</div>
        <div class="itemdatavalue itemname"><?=$item_name?></div>
    </div>
    <div class="datarow">
        <div class="itemdatalabel">Unit Type:</div>
        <div class="itemdatavalue itemunit"><?=$item_unit?></div>
    </div>
    <div class="datarow masteritemdevide">&nbsp;</div>
    <div class="datarow">
        <div class="itemtemplatelabel">Proof Template:</div>
        <div class="itemteplatevalue <?=empty($proof_template) ? 'empty' : ''?>">
            <?php if (empty($proof_template)) { ?>
                No File
            <?php } else { ?>
                <img src="/img/masterinvent/opentemplate_btn.png" alt="Open template" data-src="<?=$proof_template?>"/>
            <?php } ?>
        </div>
    </div>
    <div class="datarow">
        <div class="itemtemplatelabel">Plate Template:</div>
        <div class="itemteplatevalue <?=empty($plate_template) ? 'empty' : ''?>">
            <?php if (empty($plate_template)) { ?>
                No File
            <?php } else { ?>
                <img src="/img/masterinvent/opentemplate_btn.png" alt="Open template" data-src="<?=$plate_template?>"/>
            <?php } ?>
        </div>
    </div>
    <div class="datarow">
        <div class="itemtemplatelabel">Box Template:</div>
        <div class="itemteplatevalue <?=empty($box_template) ? 'empty' : ''?>">
            <?php if (empty($box_template)) { ?>
                No File
            <?php } else { ?>
                <img src="/img/masterinvent/opentemplate_btn.png" alt="Open template" data-src="<?=$box_template?>"/>
            <?php } ?>
        </div>
    </div>

</div>