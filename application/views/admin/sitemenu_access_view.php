<select class="sitesaccessselect" data-menuitem="<?=$menu_item?>">
    <?php foreach ($brands as $row) { ?>
        <option value="<?=$row['key']?>" <?=$row['key']==$brand ? 'selected="selected"' : ''?>><?=$row['value']?></option>
    <?php } ?>
</select>