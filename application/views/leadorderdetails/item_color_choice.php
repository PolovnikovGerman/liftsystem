<select class="orderitemcolors" data-orderitem="<?=$order_item_id?>" data-field="item_color" data-item="<?=$item_id?>">
    <?php foreach ($colors as $row) { ?>
    <option value="<?=$row?>" <?=($row==$item_color ? 'selected="selected"' : '')?>><?=$row?></option>
    <?php } ?>
</select>