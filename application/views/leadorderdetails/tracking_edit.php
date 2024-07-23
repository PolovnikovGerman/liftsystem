<div class="trackingdataarea">
    <div class="trackingdataheader"><?=QTYOutput($qty)?> <?=$item?> <span class="addnewtrack" data-orderitem="<?=$order_item?>">[add new]</span></div>
    <div class="trackingdatabody" data-orderitem="<?=$order_item?>"><?=$trackbody?></div>
    <div class="trackingdatafooter">
        <?php if ($remind>=0) { ?>
            <div class="nontracked" data-orderitem="<?=$order_item?>"><?=$remind?> Remaining</div>
        <?php } else { ?>
            <div class="nontracked" data-orderitem="<?=$order_item?>">0 Remaining  (+<?=abs($remind)?> extra pieces) </div>
        <?php } ?>

        <div class="shipdate"><?=$shipdate?></div>
    </div>
</div>