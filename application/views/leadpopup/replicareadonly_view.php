<div class="lead_popup_replicatiltle">Reps:</div>
<div class="lead_popup_replicausrarea">
<?php $usrid=1;?>
<?php foreach ($repl as $row) {?>
        <div class="lead_popup_replicausrname"><?=$row['user_leadname']?></div>
    <?php $usrid++?>
<?php } ?>
</div>
