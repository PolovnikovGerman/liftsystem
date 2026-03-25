<?php $numpp=1;?>
<?php foreach ($proofs as $proof) : ?>
    <div class="datarow <?=$numpp%2==0 ? 'greydatarow' : 'whitedatarow'?>" data-task="<?=$proof['email_id']?>">
        <div class="newunassign_date_dat"><?=date('m/d/y', strtotime($proof['email_date']));?></div>
        <div class="proofrequests_customer_dat truncateoverflowtext" data-event="hover" data-css="itemdetailsballonbox"
             data-bgcolor="#FFFFFF" data-bordercolor="#000" data-position="auto" data-textcolor="#000" data-timer="4000" data-delay="1000"
             data-balloon="<?=empty($proof['email_sendercompany']) ? $proof['email_sender'] : $proof['email_sendercompany']?>">
            <?=empty($proof['email_sendercompany']) ? $proof['email_sender'] : $proof['email_sendercompany']?>
        </div>
        <div class="proofrequests_qty_dat"><?=QTYOutput($proof['email_qty'])?></div>
        <div class="proofrequests_item_dat truncateoverflowtext" data-event="hover" data-css="itemdetailsballonbox"
             data-bgcolor="#FFFFFF" data-bordercolor="#000" data-position="auto" data-textcolor="#000" data-timer="4000" data-delay="1000"
             data-balloon="<?=$proof['email_item_name']?>">
            <?=$proof['email_item_name']?>
        </div>
    </div>
    <?php $numpp++;?>
<?php endforeach; ?>
