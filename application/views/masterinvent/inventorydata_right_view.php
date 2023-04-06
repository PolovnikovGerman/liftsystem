<?php $coloridx=0;?>
<?php $coloridx=0;?>
<?php  foreach ($lists as $list) {?>
    <?php if ($list['item_flag']==1) { ?>
        <div class="inventorydatarow masteritem <?=$expand==1 ? 'expand' : ''?>">
    <?php } else { ?>
        <div class="inventorydatarow itemcolor <?=$coloridx%2==0 ? 'whitedatarow' : 'greydatarow'?>">
        <?php $coloridx++;?>
    <?php } ?>
    <div class="masterinventavgprice <?=$list['totalclass']?>" data-item="<?=$list['id']?>"><?=MoneyOutput($list['price'],3)?></div>
    <div class="masterinventhistory" data-item="<?=$list['id']?>"><?= $list['item_flag']==1 ?  '&nbsp;' :  '<i class="fa fa-question-circle" aria-hidden="true"></i>' ?></div>
    <div class="masterinventtotalval <?=$list['totalclass']?>"><?=MoneyOutput($list['total'])?></div>

    </div>
<?php } ?>