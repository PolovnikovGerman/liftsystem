<div class="accreceiv-content-left">
    <div class="accreceiv-owndetails-head">
        <div class="accreceiv-owndetails-headnum">#</div>
        <?php if ($ownsort=='batch_due') { ?>
            <div class="accreceiv-owndetails-headdue ownsort" data-sort="batch_due">Due <span><i class="fa <?=$owndir=='desc' ? 'fa-sort-amount-desc' : 'fa-sort-amount-asc'?>"></i></span></div>
        <?php } else { ?>
            <div class="accreceiv-owndetails-headdue ownsort" data-sort="batch_due">Due <span></span></div>
        <?php } ?>
        <?php if ($ownsort=='balance') { ?>
            <div class="accreceiv-owndetails-headbalance ownsort" data-sort="balance">Balance <span><i class="fa <?=$owndir=='desc' ? 'fa-sort-amount-desc' : 'fa-sort-amount-asc'?>"></i></span></div>
        <?php } else { ?>
            <div class="accreceiv-owndetails-headbalance ownsort" data-sort="balance">Balance <span></span></div>
        <?php } ?>
        <div class="accreceiv-owndetails-headrunningtotal">Running Total</div>
        <?php if ($ownsort=='order_num') { ?>
            <div class="accreceiv-owndetails-headorder ownsort" data-sort="order_num">Order <span><i class="fa <?=$owndir=='desc' ? 'fa-sort-amount-desc' : 'fa-sort-amount-asc'?>"></i></span></div>
        <?php } else { ?>
            <div class="accreceiv-owndetails-headorder ownsort" data-sort="order_num">Order <span></span></div>
        <?php } ?>
        <?php if ($ownsort=='customer_name') { ?>
            <div class="accreceiv-owndetails-headcustomer ownsort" data-sort="customer_name">Customer <span><i class="fa <?=$owndir=='desc' ? 'fa-sort-amount-desc' : 'fa-sort-amount-asc'?>"></i></span></div>
        <?php } else { ?>
            <div class="accreceiv-owndetails-headcustomer ownsort" data-sort="customer_name">Customer <span></span></div>
        <?php } ?>
        <?php if ($ownsort=='owntype') { ?>
            <div class="accreceiv-owndetails-headtype ownsort" data-sort="owntype">Type <span><i class="fa <?=$owndir=='desc' ? 'fa-sort-amount-desc' : 'fa-sort-amount-asc'?>"></i></span></div>
        <?php } else { ?>
            <div class="accreceiv-owndetails-headtype ownsort" data-sort="owntype">Type <span></span></div>
        <?php } ?>
        <div class="accreceiv-owndetails-headapproval">Approval</div>
        <div class="accreceiv-owndetails-headstatus">Status</div>
    </div>
    <div class="accreceiv-owndetails-body">
        <?php if (count($owns)==0) { ?>
            <div class="accreceiv-owndetails-bodyrow empty">No orders</div>
        <?php } else { ?>
            <?php $numpp=1;?>
            <?php $startdue = ''?>
            <?php foreach ($owns as $own) { ?>
                <div class="accreceiv-owndetails-bodyrow <?=$numpp%2 == 0 ? 'greydatarow' : 'whitedatarow'?>">
                    <div class="accreceiv-owndetails-bodynum"><?=$numpp?></div>
                    <div class="accreceiv-owndetails-bodydue <?=$own['batch_due'] < $daystart ? 'pastdue' : ''?>">
                        <?=date('m/d/y', $own['batch_due'])?>
                    </div>
                    <div class="accreceiv-owndetails-bodybalance"><?=TotalOutput($own['balance'])?></div>
                    <div class="accreceiv-owndetails-bodyrunningtotal"><?=MoneyOutput($own['rundebt'],0)?></div>
                    <div class="accreceiv-owndetails-bodyorder" data-order="<?=$own['order_id']?>"><?=$own['order_num']?></div>
                    <div class="accreceiv-owndetails-bodycustomer"><?=$own['customer_name']?></div>
                    <div class="accreceiv-owndetails-bodytype <?=$own['typeclass']?>"><?=$own['type']?></div>
                    <div class="accreceiv-owndetails-bodyapproval <?=$own['approved']==0 ? 'notapproved' : ''?>"><?=$own['approved']==0 ? 'Not Approved' : 'Approved'?></div>
                    <div class="accreceiv-owndetails-bodystatus">
                        <select class="debtstatus" data-order="<?=$own['order_id']?>">
                            <option value="">---</option>
                            <option value="no_reply" <?=$own['debt_status']=='no_reply' ? 'selected="selected"' : ''?>>Contacted, No Reply</option>
                            <option value="customer_check" <?=$own['debt_status']=='customer_check' ? 'selected="selected"' : ''?>>Customer Checking</option>
                            <option value="future_payment" <?=$own['debt_status']=='future_payment' ? 'selected="selected"' : ''?>>Payment Being Sent</option>
                            <option value="cc_declined" <?=$own['debt_status']=='cc_declined' ? 'selected="selected"' : ''?>>Credit Card Declined</option>
                            <option value="cancelled" <?=$own['debt_status']=='cancelled' ? 'selected="selected"' : ''?>>Cancelled</option>
                        </select>
                    </div>
                </div>
                <?php $numpp++;?>
            <?php } ?>
        <?php } ?>
    </div>
</div>
<div class="accreceiv-content-center">
    <div class="accreceiv-refunddetails-head">
        <div class="accreceiv-refunddetails-headnum">#</div>
        <?php if ($refundsort=='order_date') { ?>
            <div class="accreceiv-refunddetails-headorderdate refundsort" data-sort="order_date">Order Date <span><i class="fa <?=$refunddir=='desc' ? 'fa-sort-amount-desc' : 'fa-sort-amount-asc'?>"></i> </span></div>
        <?php } else { ?>
            <div class="accreceiv-refunddetails-headorderdate refundsort" data-sort="order_date">Order Date</div>
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
        <?php if ($refundsort=='customer_name') { ?>
            <div class="accreceiv-refunddetails-headcustomer refundsort" data-sort="customer_name">Customer <span><i class="fa <?=$refunddir=='desc' ? 'fa-sort-amount-desc' : 'fa-sort-amount-asc'?>"></i> </span></div>
        <?php } else { ?>
            <div class="accreceiv-refunddetails-headcustomer refundsort" data-sort="customer_name">Customer</div>
        <?php } ?>
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
<!--                    <div class="accreceiv-refunddetails-bodytype --><?//=$refund['typeclass']?><!--">--><?//=$refund['type']?><!--</div>-->
                </div>
                <?php $numpp++; ?>
            <?php } ?>
        <?php } ?>
    </div>

</div>
