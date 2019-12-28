<div class="lead_popup_replicatiltle">Reps:</div>
<div class="lead_popup_replicausrbgn">&nbsp;</div>
<?php foreach ($repl as $row) {?>
    <div class="lead_popup_replicausrarea">
        <div class="lead_popup_replicausrchk" data-user="<?=($row['user_id'])?>" data-replic="<?=$row['user_leadname']?>">
            <img src="/img/delete-artloc.png"/>
        </div>
        <div class="lead_popup_replicausrname"><?=$row['user_leadname']?></div>
    </div>
<?php } ?>
<div class="lead_popup_replicausrend">&nbsp;</div>
<div class="lead_popup_addreplica">
    <img alt="Add" src="/img/leads/addnew_btn.png">
</div>
