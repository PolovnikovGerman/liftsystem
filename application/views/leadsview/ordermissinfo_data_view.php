<?php $numpp = 1; ?>
<?php foreach ($orders as $order) : ?>
    <div class="datarow <?=$numpp%2==0 ? 'greydatarow' : 'whitedatarow'?> missordersdatarow" data-order="<?=$order['order_id']?>">
        <div class="missordernumber_dat" data-order="<?=$order['order_id']?>"><?=$order['order_num']?></div>
        <div class="missordercustomer_dat truncateoverflowtext"><?=$order['customer']?></div>
        <div class="missordershipaddr_dat <?=$order['shipclass']?>">
            <?php if (empty($order['ship'])) : ?>
                Missing
            <?php else : ?>
                <img src="/img/leads/artstep-tick.svg" alt="Ready"/>
            <?php endif; ?>
        </div>
        <div class="missorderpayment_dat <?=$order['paymentclass']?>">
            <?php if ($order['paymentclass']=='missing') : ?>
            <?=empty($order['payment']) ? '-' : MoneyOutput($order['payment'],0)?>
            <?php else : ?>
                <img src="/img/leads/artstep-tick.svg" alt="Ready"/>
            <?php endif; ?>
        </div>
        <div class="missorderart_dat <?=$order['artclass']?>">
            <?php if (empty($order['art'])) : ?>
            Missing
            <?php else : ?>
            <img src="/img/leads/artstep-tick.svg" alt="Ready"/>
            <?php endif; ?>
        </div>
        <div class="missorderapproval_dat <?=$order['proofclass']?>">
            <?=empty($order['proof']) ? 'Not Approved' : 'Approved'?>
        </div>
    </div>
    <?php $numpp++; ?>
<?php endforeach; ?>
