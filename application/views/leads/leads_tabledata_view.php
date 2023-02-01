<?php $nrow=0; ?>
<?php foreach ($data as $row) {?>
    <div class="lead_datarow <?=$row['separate']?> <?=($row['leadrow_class']=='' ? ($nrow%2==0 ? 'whitedatarow' : 'greydatarow') : $row['leadrow_class'])?> <?=$row['dateclass']?>" data-lead="<?=$row['lead_id']?>">
        <div class="leadpriority">
            <?=$row['lead_priority_icon']?>
        </div>
        <div class="leadnumber"><?=$brand=='SR' ? 'D' : 'L'?><?=str_pad($row['lead_number'],5,'0',STR_PAD_LEFT)?></div>
        <div class="leaddate"><?=$row['out_date']?></div>
        <div class="leadvalue"><?=$row['out_value']?></div>
        <div class="leadcustomer"><?=$row['contact']?></div>
        <div class="leadqty"><?=$row['lead_itemqty']?></div>
        <div class="leaditem <?=$row['itemshow_class']?>"><?=$row['out_lead_item']?></div>
        <div class="leadrep"><?=$row['usr_data']?></div>
    </div>
    <?php $nrow++;?>
<?php } ?>
