<div class="lead_popup_replicatiltle">Reps:</div>
<!--<div class="lead_popup_replicausrbgn">&nbsp;</div>-->
<div class="lead_popup_replicausrarea">
<?php foreach ($repl as $row) {?>
    <div class="lead_popup_replicausrchk" data-user="<?=($row['user_id'])?>" data-replic="<?=$row['user_leadname']?>">
        <img src="/img/leads/delete-artloc.png"/>
    </div>
    <div class="lead_popup_replicausrname"><?=$row['user_leadname']?></div>
<?php } ?>
</div>
<!--<div class="lead_popup_replicausrend">&nbsp;</div>-->
<div class="lead_popup_addreplica <?=$brand=='SR' ? 'relieverstab' : ''?>">
    <?php if ($brand=='SR') {?>
        <img alt="Add" src="/img/leads/add_replica_relivers.png"/>
    <?php } else { ?>
        <img alt="Add" src="/img/leads/addnew_btn.png">
    <?php } ?>

</div>
