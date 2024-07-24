<div class="trackingdatafooter <?=$completed==1 ? 'completed' : ''?>">
    <?php if ($completed==0) { ?>
        <div class="nontracked"><?=$remind?> Remaining</div>
        <div class="shipdate"><?=$shipdate?></div>
    <?php } else { ?>
        <div class="completedfull">100% FULFILLED <?=$remind == 0 ? '' : '(+'.abs($remind).' extra pieces)'?></div>
    <?php } ?>
</div>

