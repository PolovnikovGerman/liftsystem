<div class="accreceiv-content-left sigmasystem">
    <div class="accreceiv-owndetails-head">
        <div class="accreceiv-owndetails-headnum">#</div>
        <div class="accreceiv-owndetails-headrunningtotal">Running Total</div>
        <div class="accreceiv-owndetails-headdue ownsort <?=$ownsort=='batch_due' ? 'activesortsigma' : ''?>" data-sort="batch_due">Due </div>
        <div class="accreceiv-owndetails-headbalance ownsort <?=$ownsort=='balance' ? 'activesortsigma' : ''?>" data-sort="balance">Balance</div>
        <div class="accreceiv-owndetails-headbrand ownsort <?=$ownsort=='brand' ? 'activesortsigma' : ''?>" data-sort="brand"><span></span></div>
        <div class="accreceiv-owndetails-headordersigma ownsort <?=$ownsort=='order_num' ? 'activesortsigma' : ''?>" data-sort="order_num">Order</div>
        <div class="accreceiv-owndetails-headponumber ownsort <?=$ownsort=='customer_ponum' ? 'activesortsigma' : ''?>" data-sort="customer_ponum">PO #</div>
        <div class="accreceiv-owndetails-headcustomer ownsort <?=$ownsort=='customer_name' ? 'activesortsigma' : ''?>" data-sort="customer_name">Customer <span></span></div>
        <div class="accreceiv-owndetails-headtype ownsort <?=$ownsort=='owntype' ? 'activesortsigma' : ''?>" data-sort="owntype">Type <span></span></div>
        <div class="accreceiv-owndetails-headapproval ownsort <?=$ownsort=='ownapprove' ? 'activesortsigma' : ''?>" data-sort="ownapprove">Approval</div>
        <div class="accreceiv-owndetails-headstatus ownsort <?=$ownsort=='debt_status' ? 'activesortsigma' : ''?>" data-sort="debt_status">Status</div>
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
                    <div class="accreceiv-owndetails-bodybrand <?=$own['brand']=='SR' ? 'stressrelievers' : 'bluetrack'?>"><?=$own['brand']?></div>
                    <div class="accreceiv-owndetails-bodyordersigma" data-brand="<?=$own['brand']?>" data-order="<?=$own['order_id']?>"><?=$own['order_num']?></div>
                    <div class="accreceiv-owndetails-bodyponumber" title="<?=$own['customer_ponum']?>"><?=$own['customer_ponum']?></div>
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
<div class="accreceiv-content-center sigmasystem">
    <div class="accreceiv-refunddetails-head">
        <div class="accreceiv-refunddetails-headnum">#</div>
        <div class="accreceiv-refunddetails-headorderdate refundsort <?=$refundsort=='order_date' ? 'activesortsigma' : ''?>" data-sort="order_date">Order Date</div>
        <div class="accreceiv-refunddetails-headbalance refundsort <?=$refundsort=='balance' ? 'activesortsigma' : ''?>" data-sort="balance">Refund</div>
        <div class="accreceiv-refunddetails-headbrand refundsort  <?=$refundsort=='brand' ? 'activesortsigma' : ''?>" data-sort="brand"></div>
        <div class="accreceiv-refunddetails-headordersigma refundsort <?=$refundsort=='order_num' ? 'activesortsigma' : ''?>" data-sort="order_num">Order</div>
        <div class="accreceiv-refunddetails-headcustomer refundsort <?=$refundsort=='customer_name' ? 'activesortsigma' : ''?>" data-sort="customer_name">Customer</div>
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
                    <div class="accreceiv-refunddetails-bodybrand <?=$refund['brand']=='SR' ? 'stressrelievers' : 'bluetrack'?>"><?=$refund['brand']?></div>
                    <div class="accreceiv-refunddetails-bodyordersigma" data-brand="<?=$refund['brand']?>" data-order="<?=$refund['order_id']?>"><?=$refund['order_num']?></div>
                    <div class="accreceiv-refunddetails-bodycustomer"><?=$refund['customer_name']?></div>
                </div>
                <?php $numpp++; ?>
            <?php } ?>
        <?php } ?>
    </div>

</div>
