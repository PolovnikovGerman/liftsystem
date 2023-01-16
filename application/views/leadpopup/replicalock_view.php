<div class="lead_popup_replicatiltle">Reps:</div>
<div class="lead_popup_replicausrbgn">&nbsp;</div>
<?php $usrid=1;?>
<?php foreach ($repl as $row) {?>
    <div class="lead_popup_replicausrarea">
        <div class="lead_popup_replicausrname"><?=$row['user_leadname']?></div>
    </div>
    <?php $usrid++?>
<?php } ?>
<div class="lead_popup_replicausrend">&nbsp;</div>