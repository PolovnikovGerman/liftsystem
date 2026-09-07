<?php if (count($imprints)==0) : ?>
    <div class="tblitems_tr doprow whitedatarow">
        <div class="tblitems_td tblitems_item textgreen">
            Print Details:
        </div>
        <div class="tblitems_td tblitems_inforow">blank, no imprinting</div>
        <div class="tblitems_td tblitems_qty">&nbsp;</div>
        <div class="tblitems_td tblitems_each">&nbsp;</div>
        <div class="tblitems_td tblitems_subtotal">&nbsp;</div>
    </div>
<?php else : ?>
    <?php $nrow = 0;?>
    <?php foreach ($imprints as $imprint) : ?>
        <div class="tblitems_tr doprow <?=$nrow%2==0 ? 'whitedatarow' : 'greydatarow'?>">
            <?php if ($nrow==0) : ?>
                <div class="tblitems_td tblitems_item textgreen">Print Details:</div>
            <?php else : ?>
                <div class="tblitems_td tblitems_item">&nbsp;</div>
            <?php endif; ?>
            <div class="tblitems_td tblitems_inforow"><?=$imprint['imprint_description']?></div>
            <div class="tblitems_td tblitems_qty"><?=$imprint['imprint_qty']?></div>
            <div class="tblitems_td tblitems_each"><?=empty(floatval($imprint['imprint_price'])) ? '--' : MoneyOutput($imprint['imprint_price'])?></div>
            <div class="tblitems_td tblitems_subtotal"><?=$imprint['imprint_subtotal']?></div>
        </div>
    <?php $nrow++; ?>
    <?php endforeach; ?>
<?php endif; ?>
