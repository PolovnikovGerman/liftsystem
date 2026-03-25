<?php $numpp = 1;?>
<?php foreach ($quotes as $quote): ?>
    <div class="datarow <?=$numpp%2==0 ? 'greydatarow' : 'whitedatarow'?>" data-task="<?=$quote['email_id']?>">
        <div class="newunassign_date_dat"><?=date('m/d/y', strtotime($quote['email_date']));?></div>
        <div class="onlinequotes_customer_dat"><?=(empty($quote['email_sendercompany']) ? $quote['email_sender'] : $quote['email_sendercompany'])?></div>
        <div class="onlinequotes_qty_dat"><?=QTYOutput($quote['email_qty'])?></div>
        <div class="onlinequotes_item_dat"><?=$quote['email_item_name']?></div>
    </div>
    <?php $numpp++;?>
<?php endforeach; ?>
