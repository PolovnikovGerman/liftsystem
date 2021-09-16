<?php $nrow=0;?>
<?php $numpp=1;?>
<?php foreach ($data as $row) {?>
    <div class="taskdata_row <?=($nrow%2==0 ? 'greydatarow' : 'whitedatarow')?>" id="task">
        <div class="taskview_seqnum"><?=$numpp?>.</div>
        <div class="taskview_rush"><?=$row['order_rush']?></div>
        <div class="taskview_time"><?=$row['diff']?></div>
        <div class="taskview_order <?=$row['bypass_class']?> <?=$row['order_overclass']?>"
             data-event="hover" data-css="task_detailview" data-position="up" data-bgcolor="#EDEDED" data-bordercolor="#000" data-textcolor="#000" data-balloon="<?=$row['task_title']?>"
             id="<?=$row['order_disp_id']?>">
            <?=$row['order_num']?>
        </div>
        <div class="taskview_note <?=($note==1 ? 'reminderarea' : '')?>" data-taskid="<?=$row['order_disp_id']?>">
            <?=($note==1 ? '<img src="/img/art/reminder.png" alt="reminder"/>' : '')?>
        </div>
        <?php $nrow++;?>
        <?php $numpp++;?>
    </div>
<?php } ?>