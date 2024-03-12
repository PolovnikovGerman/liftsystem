<?php $nrow=0;?>
<div class="orderitemsarea" data-orderitem="<?=$order_item_id?>">
<?php foreach ($items as $row) { ?>
    <div class="items_table_line bord_b <?=($nrow%2==0 ? '' : 'items_line_gray')?>">
        <?php if ($row['item_row']==1) { ?>
            <?php if ($item_id>0) { ?>
                <!-- <div class="icon_glass newactive" data-viewsrc="/leadorder/viewitemimage?id=<?php //$item_id?>">&nbsp;</div> -->
                <div class="icon_glass newactive" data-event="hover" data-css="weekbrandtotals" data-bgcolor="#FFFFFF" data-bordercolor="#adadad"
                     data-textcolor="#FFFFFF" data-position="right" data-balloon="{ajax} /leadorder/viewitemimage?id=<?=$item_id?>">&nbsp;</div>
            <?php } else { ?>
                <div class="icon_glass">&nbsp;</div>
            <?php } ?>
        <?php } ?>
        <div class="items_content_item2">
            <?=$row['item_number']?>
            <?php if ($showinvent==1 && $row['item_row']==1) { ?>
                <div class="iteminventoryshow" data-item="<?=$order_item_id?>"><i class="fa fa-info-circle" aria-hidden="true"></i></div>
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
        <div class="items_content_subtotal_view bord_l"><?=MoneyOutput($row['item_subtotal'])?></div>
    </div>
    <?php $nrow++;?>
<?php } ?>
<?=$imprintview?>
</div>