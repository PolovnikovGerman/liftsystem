<?php $nrow=0;?>
<div class="quoteitemsarea" data-quoteitem="<?= $quote_item_id ?>">
    <?php foreach ($items as $row) { ?>
        <div class="quoteitemtabledatarow <?=($nrow%2==0 ? 'whitedatarow' : 'greydatarow')?>">
            <div class="itemnumber"><?=$row['item_number']?></div>
            <div class="itemdescription long bord_l">
                <?=htmlspecialchars($row['item_description'])?>
            </div>
            <div class="itemcolor <?=($row['item_color_add']==1 ? '' : 'bord_l') ?>">
                <?=$row['item_color'] ?>
            </div>
            <div class="itemqty">
                <?=$row['item_qty']?>
            </div>
            <div class="itemprice">
                <?=PriceOutput($row['item_price'])?>
            </div>
            <div class="quoteitemrowsubtotal" data-item="<?=$row['item_id']?>" data-quoteitem="<?= $quote_item_id ?>"><?=MoneyOutput($row['item_subtotal'])?></div>
            <div class="quoteitemremove">&nbsp;</div>
        </div>
        <?php $nrow++;?>
    <?php } ?>
    <?=$imprintview?>
</div>
