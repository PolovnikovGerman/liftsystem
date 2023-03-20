<?php $numpp=0;?>
<?php foreach ($data as $dat) { ?>
    <div class="content-row <?=$numpp%2==0 ? 'greydatarow' : 'whitedatarow'?>" data-form="<?=$dat['custom_quote_id']?>">
        <div class="numrec showformdetails"><?=$dat['numpp']?></div>
        <div class="websys <?=$dat['active']==1 ? 'active' : 'removed'?>" data-form="<?=$dat['custom_quote_id']?>">
            <?php if ($dat['active']==1) { ?>
                <!-- <i class="fa fa-trash" aria-hidden="true"></i> -->
                <i class="fa fa-eye-slash" aria-hidden="true"></i>
            <?php } else { ?>
                <i class="fa fa-undo" aria-hidden="true"></i>
            <?php } ?>
        </div>
        <div class="status <?=empty($dat['lead_id']) ? 'assignform' : ''?>">
            <?php if (empty($dat['lead_number'])) { ?>
                <img src="/img/art/assign_lead_btn.png"/>
            <?php } else { ?>
                L<?=$dat['lead_number']?>
            <?php } ?>
        </div>
        <div class="date  showformdetails"><?=date('m/d/y', strtotime($dat['date_add']))?></div>
        <div class="customname  showformdetails"><?=$dat['customer_name']?></div>
        <div class="custommail showformdetails"><?=$dat['customer_email']?></div>
        <div class="customphone showformdetails"><?=$dat['customer_phone']?></div>
        <div class="itemdescription showformdetails" data-event="<?=$event?>" data-css="customform_popmessage" data-position="left" data-balloon="<?=$dat['shape_desription']?>">
            <?=$dat['shape_desription']?>
        </div>
        <div class="itemqty showformdetails"><?=empty($dat['quota_qty']) ? '' : QTYOutput($dat['quota_qty'])?></div>
        <div class="eventdate showformdetails"><?=$dat['event_date']?></div>
    </div>
    <?php $numpp++?>
<?php } ?>
