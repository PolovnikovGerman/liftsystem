<?php $numpp = 0; ?>
<div class="imprintdataarea" data-orderitem="<?= $order_item_id ?>">
    <?php foreach ($imprints as $row) { ?>
        <?php if ($row['delflag']==0) { ?>
            <div class="items_table_line <?= $numpp % 2 == 0 ? '' : 'items_line_gray' ?>">
                <div class="items_content_item2 <?= $numpp == 0 ? 'text_green imprintdetails' : '' ?>" data-orderitem="<?= $order_item_id ?>">
                    <?= $numpp == 0 ? 'Print Details:' : '&nbsp;' ?>
                </div>
                <div class="items_content_description3 bord_l">
                    <?= $row['imprint_description'] ?>            
                </div>                
                <div class="items_content_qty2 bord_l">
                    <?= $row['imprint_qty'] == 0 ? '&nbsp;' : $row['imprint_qty'] ?>
                </div>
                <div class="items_content_each2 bord_l <?=$row['imprint_price_class']?>" title="<?=$row['imprint_price_title']?>">
                <?= $row['imprint_price'] == 0 ? '--' : MoneyOutput($row['imprint_price']) ?>
                </div>
                <div class="items_content_sub_total2 bord_l"><?= $row['imprint_subtotal'] ?></div>
            </div>    
            <?php $numpp++; ?>            
        <?php } ?>
    <?php } ?>
</div>
