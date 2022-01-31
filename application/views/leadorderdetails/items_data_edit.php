<?php $nrow=0;?>
<div class="orderitemsarea" data-orderitem="<?=$order_item_id?>">
<?php foreach ($items as $row) { ?>
    <div class="items_table_line bord_b <?=($nrow%2==0 ? '' : 'items_line_gray')?>">
        <div class="items_content_item2 short"><?=$row['item_number']?></div>
        <div class="itemdescription_data long bord_l">
            <input type="text" class="orderitem_description_long orderitem_input input_border_gray" data-field="item_description" data-item="<?=$row['item_id']?>" data-orderitem="<?=$row['order_item_id']?>"  value="<?=htmlspecialchars($row['item_description'])?>" />
            <?php if ($row['item_color_add']) { ?>
            <div class="itemcoloradd text_green" data-item="<?=$row['order_item_id']?>" data-orderitem="<?=$row['order_item_id']?>">+color</div>
            <?php } ?>            
        </div>
        <div class="itemcolor_data <?=($row['item_color_add']==1 ? '' : 'bord_l') ?>">
            <?=$row['out_colors']?>
        </div>
        <div class="items_content_qty2 bord_l">
            <input type="text" class="orderitem_qty input_text_right orderitem_input input_border_gray" data-field="item_qty" data-item="<?=$row['item_id']?>" data-orderitem="<?=$row['order_item_id']?>" value="<?=$row['item_qty']?>" />
        </div>
        <div class="items_content_each2 bord_l">
            <input type="text" class="orderitem_price input_text_right orderitem_input input_border_gray <?=$row['qtyinput_class']?>" title="<?=$row['qtyinput_title']?>" data-field="item_price" data-item="<?=$row['item_id']?>" data-orderitem="<?=$row['order_item_id']?>" value="<?=PriceOutput($row['item_price'])?>" />
        </div>
        <div class="items_content_sub_total2 bord_l" data-item="<?=$row['item_id']?>" data-orderitem="<?=$row['order_item_id']?>"><?=$row['item_subtotal']?></div>
        <div class="items_content_trash2 bord_l">
            <?php if ($row['item_row']==1) { ?>
            <i class="fa fa-trash" data-orderitem="<?=$row['order_item_id']?>" data-item="<?=$row['item_description']?>"></i>
            <?php } else { ?>
            &nbsp;
            <?php } ?>
        </div>
    </div>
    <?php $nrow++;?>
<?php } ?>
<?=$imprintview?>
</div>