<?php $numpp = 0 ?>
<?php foreach ($leads as $lead) : ?>
    <?php if ($lead['dateclass']=='outdate') : ?>
    <div class="dateout"><?=$lead['date']?></div>
    <?php else: ?>
    <div class="datarow <?=empty($lead['leadrow_class']) ? 'leadsrow' : ''?> <?=empty($lead['leadrow_class']) ? ($numpp%2==0 ? 'whitedatarow' : 'greydatarow') : $lead['leadrow_class']?>" data-lead="<?=$lead['lead_id']?>">
        <div class="leadnum <?=$lead['lead_priority'] ? 'leadpriority' : ''?>"><?=$lead['leadnum']?></div>
        <div class="leadcustomer truncateoverflowtext"><?=$lead['contact']?></div>
        <div class="leadqty"><?=$lead['lead_itemqty']?></div>
        <div class="leaditem <?=$lead['itemshow_class']?> truncateoverflowtext"><?=$lead['out_lead_item']?></div>
    </div>
    <?php $numpp++ ?>
    <?php endif ?>
<?php endforeach; ?>
