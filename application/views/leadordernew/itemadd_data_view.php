<?php foreach ($items as $item) : ?>
<div class="tblitems_td tblitems_additemdata">
    <select class="addnewitem" data-orderitem="<?=$item['order_item_id']?>">
        <option value="">Enter &amp; Select Item</option>
        <?php foreach ($itemslist as $list) { ?>
            <option value="<?=$list['item_id']?>"><?=$list['itemnumber']?> &ndash; <?=$list['itemname']?></option>
        <?php } ?>
    </select>
</div>
<div class="tblitems_td adddata_color">&nbsp;</div>
<div class="tblitems_td adddata_qty">&nbsp;</div>
<div class="tblitems_td adddata_price">&nbsp;</div>
<div class="tblitems_td adddata_subtotal">
    <div class="items_content_addprint" data-orderitem="<?=$item['order_item_id']?>">Imprints</div>
</div>
<div class="tblitems_td adddata_cancel" data-orderitem="<?=$item['order_item_id']?>"><i class="fa fa-trash"></i></div>
<?php endforeach; ?>