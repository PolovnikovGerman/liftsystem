<?php $numpp = 0;?>
<?php $curdate = '';?>
<?php foreach ($quotes as $quote): ?>
    <div class="datarow <?=$numpp%2==0 ? 'whitedatarow' : 'greydatarow'?> <?=$curdate!==date('m/d/y', strtotime($quote['email_date'])) && $numpp > 0 ? 'topseparaterow' : ''?>" data-task="<?=$quote['email_id']?>">
        <?php if ($curdate!==date('m/d/y', strtotime($quote['email_date']))) :?>
            <?php $curdate = date('m/d/y', strtotime($quote['email_date'])); ?>
            <div class="newunassign_date_dat"><?=$curdate?></div>
        <?php else : ?>
            <div class="newunassign_date_dat">--</div>
        <?php endif; ?>
        <div class="onlinequotes_customer_dat"><?=(empty($quote['email_sendercompany']) ? $quote['email_sender'] : $quote['email_sendercompany'])?></div>
        <div class="onlinequotes_qty_dat"><?=QTYOutput($quote['email_qty'])?></div>
        <div class="onlinequotes_item_dat"><?=$quote['email_item_name']?></div>
    </div>
    <?php $numpp++;?>
<?php endforeach; ?>
