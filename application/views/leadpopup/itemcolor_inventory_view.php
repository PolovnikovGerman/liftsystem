<div class="quoteitem_inventoryview_head">
    <div class="inventorycolor">Color</div>
    <div class="inventoryinstock">In Stock</div>
    <div class="inventoryreserv">Resv</div>
    <div class="inventoryavailable">Avail</div>
    <?php foreach ($onboats as $onboat) { ?>
        <div class="inventoryonboat"><?=$onboat['onboat_container']?> (<?=date('M d', $onboat['onboat_date'])?>)</div>
    <?php } ?>
</div>
<?php if ($itemstatus==1) { ?>
    <div class="quoteitem_inventoryview_close"><i class="fa fa-times-circle"></i></div>
<?php } ?>
<div class="quoteitem_inventoryview_body">
    <?php $numpp = 1;?>
    <?php foreach ($invents as $invent) { ?>
        <div class="datarow <?=$numpp%2==0 ? 'whitedatarow' : 'greydatarow'?>" data-itemcolor="<?=$invent['color']?>">
            <div class="inventorycolor"><?=$invent['color']?></div>
            <div class="inventorydataempty">&nbsp;</div>
            <div class="inventoryinstock <?=$invent['stockclass']?> inventorydatacell"><?=$invent['instock']?></div>
            <div class="inventoryreserv inventorydatacell"><?=$invent['reserved']?></div>
            <div class="inventoryavailable inventorydatacell"><?=$invent['available']?></div>
            <div class="inventorydataempty">&nbsp;</div>
            <?php foreach ($onboats as $onboat) { ?>
                <div class="inventoryonboat inventorydatacell"><?=$invent['onboat'.$onboat['onboat_container']]?></div>
            <?php } ?>
        </div>
        <?php $numpp++;?>
    <?php } ?>
</div>
