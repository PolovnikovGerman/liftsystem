<div class="content-row">
    <div class="itemparamlabel vendorname">Su. Item #:</div>
    <div class="itemparamvalue editmode vendorname">
        <select class="printshopitemselect" data-item="printshop_inventory_id">
            <option value=""></option>
            <?php foreach ($itemlists as $itemlist) { ?>
                <option value="<?=$itemlist['inventory_item_id']?>" <?=$itemlist['inventory_item_id']==$item['printshop_inventory_id'] ? 'selected="selected"' : ''?>><?=$itemlist['item_num']?></option>
            <?php } ?>
        </select>
    </div>
</div>
<div class="content-row">
    <div class="itemparamlabel vendorname">Su. Item:</div>
    <div class="itemparamvalue vendorname printshop_item_name <?=empty($item['printshop_item_name']) ? 'missing_info' : ''?>"><?=$item['printshop_item_name']?></div>
</div>