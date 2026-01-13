<?php $nrow=0;?>
<?php $numpp=1;?>
<?php foreach ($data as $row) {?>
    <div class="taskdatashort_row <?=($nrow%2==0 ? 'greydatarow' : 'whitedatarow')?>">
        <div class="taskview_rush"><?=$row['order_rush']?></div>
        <div class="taskview_custom">
            <?=$row['customitem']==1 ? '<i class="fa fa-diamond" aria-hidden="true"></i>' : '&nbsp;';?>
        </div>
        <div class="taskview_time"><?=$row['diff']?></div>
        <div class="taskview_order <?=$row['bypass_class']?> <?=$row['order_overclass']?>" data-content="<?=$row['task_title']?>" id="<?=$row['order_disp_id']?>">
            <?=$row['order_num']?>
        </div>
        <?php $nrow++;?>
        <?php $numpp++;?>
    </div>
<?php } ?>