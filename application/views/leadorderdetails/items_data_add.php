<?php $nrow=0;?>
<div class="orderitemsarea" data-orderitem="<?=$order_item_id?>">
    <?php foreach ($items as $row) { ?>
        <div class="items_table_line bord_b <?=($nrow%2==0 ? '' : 'items_line_gray')?>">
            <div class="items_content_add">
                <select class="addnewitem" data-orderitem="<?=$order_item_id?>">
                    <option value="">Enter &amp; Select Item</option>
                    <?php foreach ($itemslist as $list) { ?>
                        <option value="<?=$list['item_id']?>"><?=$list['itemnumber']?> <?=$list['itemname']?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="itemcolor_adddata bord_l">&nbsp;</div>
            <div class="items_content_addqty bord_l">&nbsp;</div>
            <div class="items_content_addprice bord_l">&nbsp;</div>
            <div class="items_content_sub_total2 bord_l" data-item="<?=$row['item_id']?>" data-orderitem="<?=$row['order_item_id']?>">
                <div class="items_content_addprint" data-orderitem="<?=$row['order_item_id']?>">
                    <i class="fa fa-check-square"></i>
                </div>
                <span class="itemsubtotal"></span>
            </div>
            <div class="items_content_cancel bord_l" data-orderitem="<?=$row['order_item_id']?>"><i class="fa fa-trash"></i></div>
        </div>
        <div class="orderitem_inventoryview">&nbsp;</div>
        <?php $nrow++;?>
    <?php } ?>
</div>