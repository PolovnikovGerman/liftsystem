<?php $numpp=0;?>
<?php foreach ($data as $dat) { ?>
    <div class="content-row <?=$numpp%2==0 ? 'greydatarow' : 'whitedatarow'?>" data-form="<?=$dat['custom_quote_id']?>">
        <div class="numrec"><?=$dat['numpp']?></div>
        <div class="websys">&nbsp;</div>
        <div class="status">&nbsp;</div>
        <div class="date"><?=date('m/d/y', strtotime($dat['date_add']))?></div>
        <div class="customname"><?=$dat['customer_name']?></div>
        <div class="custommail"><?=$dat['customer_email']?></div>
        <div class="customphone"><?=$dat['customer_phone']?></div>
        <div class="itemdescription" data-event="<?=$event?>" data-css="customform_popmessage" data-position="left" data-balloon="<?=$dat['shape_desription']?>">
            <?=$dat['shape_desription']?>
        </div>
        <div class="itemqty"><?=empty($dat['quota_qty']) ? '' : QTYOutput($dat['quota_qty'])?></div>
    </div>
    <?php $numpp++?>
<?php } ?>
