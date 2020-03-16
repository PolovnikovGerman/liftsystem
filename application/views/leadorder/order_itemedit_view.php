<div class="order_itemedit_area">
    <div class="order_itemedit_title">Item</div>
    <div class="order_itemedit">
        <select class="selectorderitem" id="orderitem_id">
            <option value="">Enter & Select Item</option>
            <?php foreach ($items_list as $row) { ?>
            <option value="<?=$row['item_id']?>" <?=($row['item_id']==$item_id ? 'selected="selected"' : '' )?>><?=$row['itemnumber']?> &mdash; <?=$row['itemname']?></option>
            <?php } ?>
        </select>
    </div>
    <div class="order_itemedit_text">
        <label><?=$order_item_label?></label>
        <textarea class="orderitemsvalue"><?=$order_items?></textarea>
    </div>
    <div class="order_itemedit_save" data-orderid="<?=$order_id?>">
        <img src="/img/leadorder/saveticket.png" alt="Save"/>
    </div>
</div>