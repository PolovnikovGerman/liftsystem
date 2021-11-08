<?php $nrow=0;?>
<div class="orderitemsarea" data-orderitem="<?=$order_item_id?>">
<?php foreach ($items as $row) { ?>
    <div class="items_table_line bord_b <?=($nrow%2==0 ? '' : 'items_line_gray')?>">
        <div class="items_content_item2"><?=$row['item_number']?>
            <?php if ($row['item_row']==1) { ?>
                <?php if ($item_id>0) { ?>
                    <div class="icon_glass newactive" data-event="hover" data-bgcolor="#fff" data-bordercolor="#000" data-balloon="{ajax} /leadorder/viewitemimage?id=<?=$item_id?>">
                        <img src="/img/leadorder/icon_glass.png" alt="Glass">
                    </div>
                <?php } else { ?>
                    <div class="icon_glass">
                        <img src="/img/leadorder/icon_glass.png" alt="Glass">
                    </div>
                <?php } ?>                
            <?php } ?>
        </div>
        <div class="itemdescription_data bord_l">
            <input type="text" class="orderitem_description orderitem_input input_border_hidden" readonly="readonly" value="<?=htmlspecialchars($row['item_description'])?>" />
        </div>
        <div class="itemcolor_data bord_l"><?=(empty($row['item_color']) ? '&nbsp;' : $row['item_color'])?></div>
        <div class="items_content_qty2 bord_l">
            <input type="text" class="orderitem_qty input_text_right orderitem_input input_border_hidden" readonly="readonly"  value="<?=$row['item_qty']?>" />
        </div>
        <div class="items_content_each2 bord_l">
            <input type="text" class="orderitem_price input_text_right orderitem_input input_border_hidden <?=$row['qtyinput_class']?>" title="<?=$row['qtyinput_title']?>" readonly="readonly" value="<?=PriceOutput($row['item_price'])?>" />
        </div>
        <div class="items_content_sub_total2 bord_l"><?=$row['item_subtotal']?></div>
        <div class="items_content_trash2">&nbsp;</div>
    </div>
    <?php $nrow++;?>
<?php } ?>
<?=$imprintview?>
</div>