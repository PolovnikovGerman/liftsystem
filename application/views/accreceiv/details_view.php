<div class="accreceiv-content-left">
    <div class="accreceiv-owndetails-head">
        <div class="accreceiv-owndetails-headnum">#</div>
        <div class="accreceiv-owndetails-headrunningtotal">Running Total</div>
        <div class="accreceiv-owndetails-headdue ownsort <?=$ownsort=='batch_due' ? 'activesortbt' : ''?>" data-sort="batch_due">Due </div>
        <div class="accreceiv-owndetails-headbalance ownsort <?=$ownsort=='balance' ? 'activesortbt' : ''?>" data-sort="balance">Balance</div>
        <div class="accreceiv-owndetails-headorder ownsort <?=$ownsort=='order_num' ? 'activesortbt' : ''?>" data-sort="order_num">Order</div>
        <div class="accreceiv-owndetails-headcustomer ownsort <?=$ownsort=='customer_name' ? 'activesortbt' : ''?>" data-sort="customer_name">Customer <span></span></div>
        <div class="accreceiv-owndetails-headtype ownsort <?=$ownsort=='owntype' ? 'activesortbt' : ''?>" data-sort="owntype">Type <span></span></div>
        <div class="accreceiv-owndetails-headapproval ownsort <?=$ownsort=='ownapprove' ? 'activesortbt' : ''?>" data-sort="ownapprove">Approval</div>
        <div class="accreceiv-owndetails-headstatus ownsort <?=$ownsort=='debt_status' ? 'activesortbt' : ''?>" data-sort="debt_status">Status</div>
    </div>
    <div class="accreceiv-owndetails-body">
        <?php if (count($owns)==0) { ?>
            <div class="accreceiv-owndetails-bodyrow empty">No orders</div>
        <?php } else { ?>
            <?php $numpp=1;?>
            <?php $startdue = $owns[0]['dueclass']?>
            <?php $starttype = $owns[0]['type'];?>
            <?php $starapprov = $owns[0]['approved'];?>
            <?php $starstatus = $owns[0]['debt_status'];?>
            <?php foreach ($owns as $own) { ?>
                <div class="accreceiv-owndetails-bodyrow <?=$numpp%2 == 0 ? 'greydatarow' : 'whitedatarow'?> <?=$own['dueclass']!==$startdue ? 'separated' : ''?>">
                    <div class="accreceiv-owndetails-bodynum"><?=$numpp?></div>
                    <div class="accreceiv-owndetails-bodyrunningtotal"><?=MoneyOutput($own['rundebt'],0)?></div>
                    <div class="accreceiv-owndetails-bodydue <?=$own['dueclass']?>">
                        <?=date('m/d/y', $own['batch_due'])?>
                    </div>
                    <div class="accreceiv-owndetails-bodybalance"><?=TotalOutput($own['balance'])?></div>
                    <div class="accreceiv-owndetails-bodyorder" data-order="<?=$own['order_id']?>"><?=$own['order_num']?></div>
                    <div class="accreceiv-owndetails-bodycustomer"><?=$own['customer_name']?></div>
                    <div class="accreceiv-owndetails-bodytype <?=$own['typeclass']?> <?=$starttype!==$own['type'] ? 'separated' : ''?> <?=$ownsort=='owntype' ? 'sorted' : ''?>"><?=$own['type']?></div>
                    <div class="accreceiv-owndetails-bodyapproval <?=$own['approved']==0 ? 'notapproved' : ''?> <?=$starapprov!==$own['approved'] ? 'separated' : ''?> <?=$ownsort=='ownapprove' ? 'sorted' : ''?>"><?=$own['approved']==0 ? 'Not Approved' : 'Approved'?></div>
                    <div class="accreceiv-owndetails-bodystatus <?=$starstatus!==$own['debt_status'] ? 'separated' : ''?>">
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
                <?php $startdue = $own['dueclass'];?>
                <?php $starttype = $own['type'];?>
                <?php $starapprov = $own['approved'];?>
                <?php $starstatus = $own['debt_status'];?>
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
