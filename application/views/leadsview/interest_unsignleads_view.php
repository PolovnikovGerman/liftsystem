<?php $numpp = 0; ?>
<?php $curdate = ''?>
<?php foreach ($leads as $lead) : ?>
    <div class="datarow <?=$numpp%2==0 ? 'whitedatarow' : 'greydatarow'?> <?=$curdate!==date('m/d/y', $lead['lead_date']) && $numpp > 0 ? 'topseparaterow' : ''?>" data-task="<?=$lead['lead_id']?>">
        <?php if ($curdate!==date('m/d/y', $lead['lead_date'])) :?>
            <?php $curdate = date('m/d/y', $lead['lead_date']); ?>
            <div class="newunassign_date_dat"><?=$curdate?></div>
        <?php else : ?>
            <div class="newunassign_date_dat">--</div>
        <?php endif; ?>
        <div class="sbcustomform_customer_dat truncateoverflowtext"><?=empty($lead['lead_company']) ? $lead['lead_customer'] : $lead['lead_company']?></div>
        <div class="sbcustomform_qty_dat"><?=QTYOutput($lead['lead_itemqty'])?></div>
        <div class="sbcustomform_item_dat truncateoverflowtext" data-event="hover" data-css="itemdetailsballonbox"
             data-bgcolor="#FFFFFF" data-bordercolor="#000" data-position="auto" data-textcolor="#000" data-timer="4000" data-delay="1000"
             data-balloon="<?=$lead['lead_item']?>">
            <?=$lead['lead_item']?>
        </div>
    </div>
    <?php $numpp++; ?>
<?php endforeach; ?>
