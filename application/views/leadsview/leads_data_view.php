<?php $numpp = 0 ?>
<?php foreach ($leads as $lead) : ?>
    <?php if ($lead['dateclass']=='outdate') : ?>
    <div class="dateout"><?=$lead['date']?></div>
    <?php else: ?>
    <div class="datarow <?=empty($lead['leadrow_class']) ? ($numpp%2==0 ? 'whitedatarow' : 'greydatarow') : $lead['leadrow_class']?>" data-lead="<?=$lead['lead_id']?>">
        <div class="leadnum <?=$lead['lead_priority'] ? 'leadpriority' : ''?>"><?=$lead['leadnum']?></div>
        <div class="leadcustomer"><?=$lead['contact']?></div>
        <div class="leadqty"><?=$lead['lead_itemqty']?></div>
        <div class="leaditem <?=$lead['itemshow_class']?>"><?=$lead['out_lead_item']?></div>
        <?php if (empty($lead['usrpopupus'])) : ?>
            <div class="leadreplica"><?=$lead['usr_data']?></div>
        <?php else: ?>
            <div class="leadreplica" data-event="hover" data-css="itemdetailsballonbox" data-bgcolor="#FFFFFF" data-bordercolor="#000" data-position="up" data-textcolor="#000"
                 data-balloon="<?=$lead['usrpopupus']?>" data-timer="4000" data-delay="1000"><?=$lead['usr_data']?></div>
        <?php endif; ?>
    </div>
    <?php $numpp++ ?>
    <?php endif ?>
<?php endforeach; ?>
