<select class="inventoryitemselect" <?=$mode=='edit' ? '' : 'disabled="disabled"'?>>
    <option value="" <?=$printshop_item_id=='' ? 'selected="selected"' : '' ?>>...</option>
    <?php foreach ($inventory_list as $row) { ?>
        <option value="<?=$row['printshop_item_id']?>" <?=$row['printshop_item_id']==$printshop_item_id ? 'selected="selected"' : ''?>><?=$row['item_name']?></option>
    <?php } ?>
</select>