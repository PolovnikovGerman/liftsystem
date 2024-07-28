<div class="trackingdataarea">
    <div class="trackingdataheader"><?=QTYOutput($qty)?> <?=$item?> </div>
    <div class="trackingdatabody" data-orderitem="<?=$order_item?>" data-color="<?=$item_color?>"><?=$trackbody?></div>
    <div class="trackingdatafooter">
        <?php if ($remind>=0) { ?>
            <div class="nontracked" data-orderitem="<?=$order_item?>" data-color="<?=$item_color?>"><?=$remind?> Remaining</div>
        <?php } else { ?>
            <div class="nontracked" data-orderitem="<?=$order_item?>" data-color="<?=$item_color?>">0 Remaining  (+<?=abs($remind)?> extra pieces) </div>
        <?php } ?>
        <div class="shipdate"><?=$shipdate?></div>
    </div>
</div>