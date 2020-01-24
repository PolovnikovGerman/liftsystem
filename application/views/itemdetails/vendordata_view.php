<div class="vendor_txtdat">
    <div class="vendor_title">Vendor:</div>
    <div class="vendor_value">
        <?php if ($mode=='edit') { ?>
            <input type="text" id="vendor_name" class="vendor_valueinp vendorinputvalues" data-entity="vendor" data-fld="vendor_name" value="<?=$vendor['vendor_name']?>"/>
        <?php } else { ?>
            <?=$vendor['vendor_name']?>
        <?php } ?>
    </div>
</div>
<div class="vendor_txtdat">
    <div class="vendor_item">Vend Item #:</div>
    <div class="vendor_itemval">
        <?php if ($mode=='edit') { ?>
            <input type="text" id="vendor_item_number" class="vendor_itemvalinp vendorinputvalues" data-entity="vendor" data-fld="vendor_item_number" value="<?=$vendor['vendor_item_number']?>"/>
        <?php } else { ?>
            <?=$vendor['vendor_item_number']?>
        <?php } ?>
    </div>
</div>
<div class="vendor_txtdat">
    <div class="vendor_item">Item Zip:</div>
    <div class="vendor_itemval">
        <?php if ($mode=='edit') { ?>
            <input type="text" class="vendor_itemvalinp vendorinputvalues" style="width: 62px;" data-entity="vendor" data-fld="vendor_item_zipcode" value="<?=$vendor['vendor_item_zipcode']?>"/>
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
            <textarea class="vendor_notestxt vendorinputvalues" data-entity="vendor" data-fld="vendor_item_notes"><?=$vendor['vendor_item_notes']?></textarea>
        <?php } else { ?>
            <?=$vendor['vendor_item_notes']?>
        <?php } ?>
    </div>
</div>
