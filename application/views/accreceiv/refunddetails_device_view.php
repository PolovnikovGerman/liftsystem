<div class="accreceiv-refunddetails-head">
    <div class="accreceiv-refunddetails-headnum">#</div>
    <?php if ($refundsort=='order_date') { ?>
        <div class="accreceiv-refunddetails-headorderdate refundsort" data-sort="order_date">Order Date <span><i class="fa <?=$refunddir=='desc' ? 'fa-sort-amount-desc' : 'fa-sort-amount-asc'?>"></i> </span></div>
    <?php } else { ?>
        <div class="accreceiv-refunddetails-headorderdate refundsort" data-sort="order_date">Date</div>
    <?php } ?>
    <?php if ($refundsort=='balance') { ?>
        <div class="accreceiv-refunddetails-headbalance refundsort" data-sort="balance">Refund <span><i class="fa <?=$refunddir=='desc' ? 'fa-sort-amount-desc' : 'fa-sort-amount-asc'?>"></i> </span></div>
    <?php } else { ?>
        <div class="accreceiv-refunddetails-headbalance refundsort" data-sort="balance">Refund</div>
    <?php } ?>
    <?php if ($refundsort=='order_num') { ?>
        <div class="accreceiv-refunddetails-headorder refundsort" data-sort="order_num">Order <span><i class="fa <?=$refunddir=='desc' ? 'fa-sort-amount-desc' : 'fa-sort-amount-asc'?>"></i> </span></div>
    <?php } else { ?>
        <div class="accreceiv-refunddetails-headorder refundsort" data-sort="order_num">Order</div>
    <?php } ?>
    <div class="accreceiv-refunddetails-headcustomer">Customer</div>
</div>
<div class="accreceiv-refunddetails-body">
    <?php if (count($refunds)==0) { ?>
        <div class="accreceiv-refunddetails-bodyrow empty">No orders</div>
    <?php } else { ?>
        <?php $numpp=1;?>
        <?php foreach ($refunds as $refund) { ?>
            <div class="accreceiv-refunddetails-bodyrow <?=$numpp%2 == 0 ? 'greydatarow' : 'whitedatarow'?>">
                <div class="accreceiv-refunddetails-bodynum"><?=$numpp?></div>
                <div class="accreceiv-refunddetails-bodyorderdate">
                    <?=date('m/d/y', $refund['order_date'])?>
                </div>
                <div class="accreceiv-refunddetails-bodybalance">(<?=TotalOutput(abs($refund['balance']))?>)</div>
                <div class="accreceiv-refunddetails-bodyorder" data-order="<?=$refund['order_id']?>"><?=$refund['order_num']?></div>
                <div class="accreceiv-refunddetails-bodycustomer"><?=$refund['customer_name']?></div>
            </div>
            <?php $numpp++; ?>
        <?php } ?>
    <?php } ?>
</div>

