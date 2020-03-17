<div class="bl_items_content3 items_table_text">
    <div class="items_line items_line_title items_content_title">
        <div class="items_content_item">Item#</div>
        <div class="items_content_it_description">Item Description</div>
        <div class="items_content_quantity">Quantity</div>
    </div>
    <div class="items_table_line2">
        <div class="items_content_item2">            
            <input type="text" class="order_itemnumber_input input_border_hidden" style="width: 64px;" readonly="readonly" value="<?= $order_itemnumber ?>"/>
        </div>
        <div class="items_content_it_description2 bord_l">
            <input type="text" class="order_itemdescript_input input_border_hidden" readonly="readonly" value="<?= $order_items ?>"/>        
        </div>
        <div class="items_content_quantity2 bord_l">
            <input type="text" class="order_itemqty_input input_text_right input_border_gray" readonly="readonly" value="<?=$order_qty?>" />
        </div>
    </div>
</div>
