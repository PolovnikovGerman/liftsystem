<?php $nrow=0;?>
<div class="orderitemsarea" data-orderitem="<?=$order_item_id?>">
<?php foreach ($items as $row) { ?>
    <div class="items_table_line bord_b <?=($nrow%2==0 ? '' : 'items_line_gray')?>">
        <div class="items_content_item2 <?=$showinvent==1 ? 'shortinvent' : 'short'?>"><?=$row['item_number']?>
            <?php if ($showinvent==1 && $row['item_row']==1) { ?>
                <div class="iteminventoryshow" data-item="<?=$order_item_id?>"><i class="fa fa-info-circle" aria-hidden="true"></i></div>
            <?php } ?>
        </div>
        <?php if ($item_id > 0) { ?>
        <div class="itemdescription_data long bord_l">
            <input type="text" class="orderitem_description_long orderitem_input input_border_gray" data-field="item_description" data-item="<?=$row['item_id']?>" data-orderitem="<?=$row['order_item_id']?>"  value="<?=htmlspecialchars($row['item_description'])?>" />
            <?php if ($row['item_color_add']) { ?>
            <div class="itemcoloradd text_green" data-item="<?=$row['order_item_id']?>" data-orderitem="<?=$row['order_item_id']?>">+color</div>
            <?php } ?>            
        </div>
        <div class="itemcolor_data <?=($row['item_color_add']==1 ? '' : 'bord_l') ?> <?=$brand=='SR' ? 'inventcolors' : ''?>">
            <?=$row['out_colors']?>
        </div>
        <?php } else { ?>
        <div class="itemdescription_data customitemdescript long bord_l">
            <input type="text" class="orderitem_description_long customitemdescript orderitem_input input_border_gray" data-field="item_description" data-item="<?=$row['item_id']?>" data-orderitem="<?=$row['order_item_id']?>"  value="<?=htmlspecialchars($row['item_description'])?>" />
        </div>
        <?php } ?>
        <div class="items_content_qty2 bord_l">
            <input type="text" class="orderitem_qty input_text_right orderitem_input input_border_gray" data-field="item_qty" data-item="<?=$row['item_id']?>" data-orderitem="<?=$row['order_item_id']?>" value="<?=$row['item_qty']?>" />
        </div>
        <div class="items_content_each2 bord_l">
            <input type="text" class="orderitem_price input_text_right orderitem_input input_border_gray <?=$row['qtyinput_class']?>" title="<?=$row['qtyinput_title']?>" data-field="item_price" data-item="<?=$row['item_id']?>" data-orderitem="<?=$row['order_item_id']?>" value="<?=PriceOutput($row['item_price'])?>" />
        </div>
        <div class="items_content_sub_total2 bord_l" data-item="<?=$row['item_id']?>" data-orderitem="<?=$row['order_item_id']?>"><?=empty($row['item_subtotal']) ? '' : MoneyOutput($row['item_subtotal'])?></div>
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