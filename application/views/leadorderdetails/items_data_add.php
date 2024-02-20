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
        </div>
        <?php $nrow++;?>
    <?php } ?>
</div>