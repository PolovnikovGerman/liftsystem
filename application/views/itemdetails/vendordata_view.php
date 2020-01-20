<div class="vendor_txtdat">
    <div class="vendor_title">Vendor:</div>
    <div class="vendor_value">
        <?php if ($mode=='edit') { ?>
            <input type="text" id="vendor_name" name="vendor_name" class="vendor_valueinp" value="<?=$vendor['vendor_name']?>"/>
        <?php } else { ?>
            <?=$vendor['vendor_name']?>
        <?php } ?>
    </div>
</div>
<div class="vendor_txtdat">
    <div class="vendor_item">Vend Item #:</div>
    <div class="vendor_itemval">
        <?php if ($mode=='edit') { ?>
            <input type="text" id="vendor_item_number" name="vendor_item_number" class="vendor_itemvalinp" value="<?=$vendor['vendor_item_number']?>"/>
        <?php } else { ?>
            <?=$vendor['vendor_item_number']?>
        <?php } ?>
    </div>
</div>
<div class="vendor_txtdat">
    <div class="vendor_item">Item Zip:</div>
    <div class="vendor_itemval">
        <?php if ($mode=='edit') { ?>
            <input type="text" id="vendor_item_zipcode" name="vendor_item_zipcode" class="vendor_itemvalinp" style="width: 62px;" value="<?=$vendor['vendor_item_zipcode']?>"/>
        <?php } else { ?>
            <?=$vendor['vendor_item_zipcode']?>
        <?php } ?>
    </div>
</div>
<div class="vendor_txtdat">
    <div class="vendor_item">Vend Notes:</div>
</div>
<div class="vendor_txtdat">
    <div class="vendor_notes">
        <?php if ($mode=='edit') { ?>
            <textarea class="vendor_notestxt" id="vendor_item_notes" name="vendor_item_notes"><?=$vendor['vendor_item_notes']?></textarea>
        <?php } else { ?>
            <?=$vendor['vendor_item_notes']?>
        <?php } ?>
    </div>
</div>
