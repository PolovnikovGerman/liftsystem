<?php if (count($refunds)==0) : ?>
    <div class="accreceiv-refunddetails-bodyrow empty">No orders</div>
<?php else : ?>
    <?php $numpp=1;?>
    <?php $startyear = 0; ?>
    <?php foreach ($refunds as $refund) : ?>
        <?php if ($refund['yearorder']!==$startyear) : ?>
            <div class="accreceiv-refunddetails-bodyrow yeartitle"><?=$refund['yearorder']?></div>
            <?php $startyear = $refund['yearorder']; ?>
        <?php endif; ?>
        <div class="accreceiv-refunddetails-bodyrow <?=$numpp%2 == 0 ? 'greydatarow' : 'whitedatarow'?>">
            <div class="accreceiv-refunddetails-bodynum"><?=$numpp?></div>
            <div class="accreceiv-refunddetails-bodyorderdate">
                <?=date('m/d', $refund['order_date'])?>
            </div>
            <div class="accreceiv-refunddetails-bodybalance">(<?=TotalOutput(abs($refund['balance']),1)?>)</div>
            <div class="accreceiv-refunddetails-bodyorder" data-order="<?=$refund['order_id']?>"><?=$refund['order_num']?></div>
            <div class="accreceiv-refunddetails-bodycustomer"><?=$refund['customer_name']?></div>
        </div>
        <?php $numpp++; ?>
    <?php endforeach; ?>
<?php endif; ?>
