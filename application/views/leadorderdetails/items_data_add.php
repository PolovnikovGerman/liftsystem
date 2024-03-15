<?php $nrow=0;?>
<div class="orderitemsarea" data-orderitem="<?=$order_item_id?>">
    <?php foreach ($items as $row) { ?>
        <div class="items_table_line bord_b <?=($nrow%2==0 ? '' : 'items_line_gray')?>">
            <div class="items_content_add">
                <select class="addnewitem" data-orderitem="<?=$order_item_id?>">
                    <option value="">Enter &amp; Select Item</option>
                    <?php foreach ($itemslist as $list) { ?>
                        <option value="<?=$list['item_id']?>"><?=$list['itemnumber']?> &ndash; <?=$list['itemname']?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="itemcolor_adddata">&nbsp;</div>
            <div class="items_content_addqty">&nbsp;</div>
            <div class="items_content_addprice">&nbsp;</div>
            <div class="items_content_sub_total2 newitemcoloradd" data-item="<?=$row['item_id']?>" data-orderitem="<?=$row['order_item_id']?>">
                <div class="items_content_addprint" data-orderitem="<?=$row['order_item_id']?>">Print Details</div>
                <!-- <span class="itemsubtotal"></span> -->
            </div>
            <div class="items_content_cancel" data-orderitem="<?=$row['order_item_id']?>"><i class="fa fa-trash"></i></div>
        </div>
        <?php $nrow++;?>
    <?php } ?>
</div>