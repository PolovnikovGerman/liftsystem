<?php $nrow=0;?>
<div class="orderitemsarea" data-orderitem="<?=$order_item_id?>">
<?php foreach ($items as $row) { ?>
    <div class="items_table_line bord_b <?=($nrow%2==0 ? '' : 'items_line_gray')?>">
        <div class="items_content_item2"><?=$row['item_number']?>
            <?php if ($row['item_row']==1) { ?>
                <?php if ($item_id>0) { ?>
                    <div class="icon_glass newactive" data-viewsrc="/leadorder/viewitemimage?id=<?=$item_id?>">&nbsp;</div>
                <?php } else { ?>
                    <div class="icon_glass">&nbsp;</div>
                <?php } ?>                
            <?php } ?>
        </div>
        <div class="itemdescription_data_view bord_l">
            <?=htmlspecialchars($row['item_description'])?>
        </div>
        <div class="itemcolor_data_view bord_l"><?=(empty($row['item_color']) ? '&nbsp;' : $row['item_color'])?></div>
        <div class="items_content_qty_view bord_l">
            <?=(empty($row['item_qty']) ? '&nbsp;' : QTYOutput($row['item_qty']))?>
        </div>
        <div class="items_content_price_view bord_l">
            <?=PriceOutput($row['item_price'])?>
        </div>
        <div class="items_content_subtotal_view bord_l"><?=$row['item_subtotal']?></div>
    </div>
    <?php $nrow++;?>
<?php } ?>
<?=$imprintview?>
</div>