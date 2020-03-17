<?php $nrow=0;?>
<?php foreach ($data as $row) {?>
    <div class="weekdetailsrow <?=($nrow%2==0 ? 'grey' : 'white')?>">
        <div class="weekday"><?=$row['label']?></div>
        <div class="newleads"><?=$row['newleads']?></div>
        <div class="workleads"><?=$row['wrkleads']?></div>
        <div class="outcalls"><?=$row['outcalls']?></div>
        <div class="ordersnum"><?=$row['orders']?></div>
        <div class="ordersrevenue"><?=$row['revenue']?></div>
        <div class="ordersprofit"><?=$row['profit']?></div>
    </div>
    <?php $nrow++;?>
<?php } ?>