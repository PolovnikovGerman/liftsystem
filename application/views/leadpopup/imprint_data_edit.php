<?php $numpp = 0; ?>
<div class="imprintdataarea" data-quoteitem="<?= $quote_item_id ?>">
    <?php foreach ($imprints as $row) { ?>
        <?php if ($row['delflag']==0) { ?>
            <div class="quoteitemtabledatarow <?= $numpp % 2 == 0 ? 'whitedatarow' : 'greydatarow' ?>">
                <div class="impritdetails <?= ($numpp == 0 && $edit_mode==1) ? 'addprintdetails' : 'printdetailslabel' ?>" data-quoteitem="<?= $quote_item_id ?>">
                    <?= $numpp == 0 ? 'Print Details:' : '&nbsp;' ?>
                </div>
                <div class="imprint_description">
                    <?= $row['imprint_description'] ?>
                </div>
                <div class="imprint_qty bord_l">
                    <?= $row['imprint_qty'] == 0 ? '&nbsp;' : $row['imprint_qty'] ?>
                </div>
                <div class="imprint_price bord_l <?=$row['imprint_price_class']?>" title="<?=$row['imprint_price_title']?>">
                    <?= $row['imprint_price'] == 0 ? '--' : MoneyOutput($row['imprint_price']) ?>
                </div>
                <div class="imprint_subtotal bord_l"><?=$row['imprint_subtotal']==0 ? '' : MoneyOutput($row['imprint_subtotal']) ?></div>
            </div>
            <?php $numpp++; ?>
        <?php } ?>
    <?php } ?>
</div>
