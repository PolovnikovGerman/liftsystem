<div class="bl_items_content3 items_table_text">
    <div class="items_line items_line_title items_content_title">
        <div class="items_content_item">Item#</div>
        <div class="items_content_it_description">Item Description</div>
        <div class="items_content_quantity">Quantity</div>
    </div>
    <div class="items_table_line2">
        <div class="items_content_itemed2">
            <div class="icon_glass active">&nbsp;</div>
            <div class="searchitembynumber">
                <select class="order_itemnumber_select">
                    <option value="" <?=$item_id=='' ? 'selected="selected"' : ''?>>&mdash;</option>
                    <?php foreach ($itemslist as $row) { ?>
                    <option value="<?=$row['item_id']?>" <?=$row['item_id']==$item_id ? 'selected="selected"' : ''?>><?=$row['itemnumber']?> / <?=$row['itemname']?></option>
                    <?php } ?>
                </select>                            
            </div>
        </div>
        <div class="items_content_it_descriptioned2 bord_l">
            <input type="text" class="order_itemdescript_editinput input_border_gray inputleadorddata" value="<?= $order_items ?>" data-entity="order" data-field="order_items"/>
        </div>
        <div class="items_content_quantity2 bord_l">
            <input type="text" class="order_itemqty_input input_text_right input_border_gray inputleadorddata" value="<?= $order_qty ?>" data-entity="order" data-field="order_qty"/>
        </div>
    </div>
</div>
