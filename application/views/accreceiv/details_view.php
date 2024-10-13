<div class="accreceiv-content-left">
    <div class="accreceiv-owndetails-head">
        <div class="accreceiv-owndetails-headnum">#</div>
        <div class="accreceiv-owndetails-headrunningtotal">Running Total</div>
        <div class="accreceiv-owndetails-headdue ownsort" data-sort="batch_due">Due </div>
<!--        --><?php //=$ownsort=='batch_due' ? 'activesortbt' : ''?>
        <div class="accreceiv-owndetails-headbalance ownsort" data-sort="balance">Balance</div>
<!--        --><?php //=$ownsort=='balance' ? 'activesortbt' : ''?><!--        -->
        <div class="accreceiv-owndetails-headorder ownsort" data-sort="order_num">Order</div>
<!--        --><?php //=$ownsort=='order_num' ? 'activesortbt' : ''?><!--        -->
        <div class="accreceiv-owndetails-headcustomer ownsort" data-sort="customer_name">Customer <span></span></div>
<!--        --><?php //=$ownsort=='customer_name' ? 'activesortbt' : ''?><!--        -->
        <div class="accreceiv-owndetails-headtype ownsort " data-sort="owntype">Type <span></span></div>
<!--        --><?php //=$ownsort=='owntype' ? 'activesortbt' : ''?>
        <div class="accreceiv-owndetails-headapproval ownsort" data-sort="ownapprove">Approval</div>
<!--        --><?php //=$ownsort=='ownapprove' ? 'activesortbt' : ''?><!--        -->
        <div class="accreceiv-owndetails-headstatus ownsort" data-sort="debt_status">Status</div>
<!--        --><?php //=$ownsort=='debt_status' ? 'activesortbt' : ''?><!--        -->
    </div>
    <div class="accreceiv-owndetails-body">
        <?php if (count($owns)==0) { ?>
            <div class="accreceiv-owndetails-bodyrow empty">No orders</div>
        <?php } else { ?>
            <?php $numpp=1;?>
            <?php foreach ($owns as $own) { ?>
                <div class="accreceiv-owndetails-bodyrow <?=$numpp%2 == 0 ? 'greydatarow' : 'whitedatarow'?> <?=$own['datclass']?>">
                    <div class="accreceiv-owndetails-bodynum"><?=$numpp?></div>
                    <div class="accreceiv-owndetails-bodyrunningtotal"><?=MoneyOutput($own['rundebt'],0)?></div>
                    <div class="accreceiv-owndetails-bodydue <?=$own['dueclass']?>">
                        <?=date('m/d/y', $own['batch_due'])?>
                    </div>
                    <div class="accreceiv-owndetails-bodybalance"><?=TotalOutput($own['balance'])?></div>
                    <div class="accreceiv-owndetails-bodyorder" data-order="<?=$own['order_id']?>"><?=$own['order_num']?></div>
                    <div class="accreceiv-owndetails-bodycustomer"><?=$own['customer_name']?></div>
                    <div class="accreceiv-owndetails-bodytype <?=$own['typeclass']?>"><?=$own['type']?></div>
                    <div class="accreceiv-owndetails-bodyapproval <?=$own['approved']==0 ? 'notapproved' : ''?>"><?=$own['approved']==0 ? 'Not Approved' : 'Approved'?></div>
                    <div class="accreceiv-owndetails-bodystatus">
                        <select class="debtstatus <?=$own['debt_status']=='' ? '' : 'checked'?>" data-order="<?=$own['order_id']?>">
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
        <div class="accreceiv-refunddetails-headorderdate refundsort <?=$refundsort=='order_date' ? 'activesortbt' : ''?>" data-sort="order_date">Order Date</div>
        <div class="accreceiv-refunddetails-headbalance refundsort <?=$refundsort=='balance' ? 'activesortbt' : ''?>" data-sort="balance">Refund</div>
        <div class="accreceiv-refunddetails-headorder refundsort <?=$refundsort=='order_num' ? 'activesortbt' : ''?>" data-sort="order_num">Order</div>
        <div class="accreceiv-refunddetails-headcustomer refundsort <?=$refundsort=='customer_name' ? 'activesortbt' : ''?>" data-sort="customer_name">Customer</div>
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
