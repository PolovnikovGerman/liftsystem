<?php $numpp = 1; ?>
<?php foreach ($orders as $order) : ?>
    <div class="datarow <?=$numpp%2==0 ? 'greydatarow' : 'whitedatarow'?> missordersdatarow" data-order="<?=$order['order_id']?>">
        <div class="missordernumber_dat <?=$order['customitem']==1 ? 'customitem' : ''?>" data-order="<?=$order['order_id']?>"><?=$order['order_num']?></div>
        <div class="missorderdate_dat <?=$order['customitem']==1 ? 'customitem' : ''?>"><?=$order['order_date']?></div>
        <div class="missordercustomer_dat truncateoverflowtext <?=$order['customitem']==1 ? 'customitem' : ''?>"><?=$order['customer']?></div>
        <div class="missorderitemname_dat" data-event="hover" data-css="itemdetailsballonbox"
             data-bgcolor="#FFFFFF" data-bordercolor="#000" data-position="auto" data-textcolor="#000" data-timer="4000" data-delay="1000"
             data-balloon="<?=$order['order_item']?>">
            <i class="fa fa-search" aria-hidden="true"></i>
<!--            <img src="/img/icons/magnifier.png" alt="Item"/>-->
        </div>
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
