<?php $nrow=0;?>
<div class="quoteitemsarea" data-quoteitem="<?= $quote_item_id ?>">
    <?php foreach ($items as $row) { ?>
        <div class="quoteitemtabledatarow <?=($nrow%2==0 ? 'whitedatarow' : 'graydatarow')?>">
            <div class="itemnumber"><?=$row['item_number']?></div>
            <div class="itemdescription long bord_l">
                <input type="text" class="quoteitem_description_long quouteitem_input input_border_gray" data-field="item_description" data-item="<?=$row['item_id']?>" data-quoteitem="<?= $quote_item_id ?>" value="<?=htmlspecialchars($row['item_description'])?>" />
                <?php if ($row['item_color_add']) { ?>
                    <div class="itemcoloradd text_green" data-item="<?=$row['item_id']?>" data-quoteitem="<?= $quote_item_id ?>">+color</div>
                <?php } ?>
            </div>
            <div class="itemcolor <?=($row['item_color_add']==1 ? '' : 'bord_l') ?>">
                <?=$row['out_colors']?>
            </div>
            <div class="itemqty">
                <input type="text" class="quoteitem_qty input_text_right quouteitem_input input_border_gray" data-field="item_qty" data-item="<?=$row['item_id']?>" data-quoteitem="<?= $quote_item_id ?>" value="<?=$row['item_qty']?>" />
            </div>
            <div class="items_content_each2 bord_l">
                <input type="text" class="orderitem_price input_text_right quouteitem_input input_border_gray <?=$row['qtyinput_class']?>" title="<?=$row['qtyinput_title']?>"
                       data-field="item_price" data-item="<?=$row['item_id']?>" data-quoteitem="<?= $quote_item_id ?>" value="<?=PriceOutput($row['item_price'])?>" />
            </div>
            <div class="items_content_sub_total2 bord_l" data-item="<?=$row['item_id']?>" data-quoteitem="<?= $quote_item_id ?>"><?=$row['item_subtotal']?></div>
            <div class="items_content_trash2 bord_l">
                <?php if ($row['item_row']==1) { ?>
                    <i class="fa fa-trash" data-quoteitem="<?= $quote_item_id ?>" data-item="<?=$row['item_description']?>"></i>
                <?php } else { ?>
                    &nbsp;
                <?php } ?>
            </div>
        </div>
        <?php $nrow++;?>
    <?php } ?>
    <?=$imprintview?>
</div>