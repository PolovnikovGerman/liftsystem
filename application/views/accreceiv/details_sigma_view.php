<div class="accreceiv-content-left sigmasystem">
    <div class="accreceiv-owndetails-head">
        <div class="accreceiv-owndetails-headnum">#</div>
        <div class="accreceiv-owndetails-headapproval ownsort" data-sort="ownapprove">Approval</div>
        <!--        --><?php //=$ownsort=='ownapprove' ? 'activesortsigma' : ''?>
        <div class="accreceiv-owndetails-headtype ownsort" data-sort="owntype">Type <span></span></div>
        <!--        --><?php //=$ownsort=='owntype' ? 'activesortsigma' : ''?>
        <div class="accreceiv-owndetails-headrunningtotal">Running Total</div>
        <div class="accreceiv-owndetails-headdue ownsort" data-sort="batch_due">Due </div>
        <div class="accreceiv-owndetails-headdays"># Days</div>
        <div class="accreceiv-owndetails-headbalance ownsort" data-sort="balance">Balance</div>
        <div class="accreceiv-owndetails-headbrand ownsort" data-sort="brand"><span></span></div>
        <div class="accreceiv-owndetails-headordersigma ownsort" data-sort="order_num">Order</div>
        <div class="accreceiv-owndetails-headcustomer ownsort" data-sort="customer_name">Customer <span></span></div>
        <div class="accreceiv-owndetails-headstatus ownsort" data-sort="debt_status">Status</div>
    </div>
    <div class="accreceiv-owndetails-body">
        <?php if (count($owns)==0) { ?>
            <div class="accreceiv-owndetails-bodyrow empty">No orders</div>
        <?php } else { ?>
            <?php $numpp=1;?>
            <?php $curtype = '' ?>
            <?php foreach ($owns as $own) { ?>
                <?php if ($ownsort=='owntype') : ?>
                    <?php if ($own['type']!==$curtype) : ?>
                        <?php $rowtype = $own['type']; $curtype = $own['type']; ?>
                    <?php else: ?>
                        <?php $rowtype = '----'; ?>
                    <?php endif; ?>
                <?php else : ?>
                    <?php $rowtype = $own['type']; ?>
                <?php endif; ?>
                <div class="accreceiv-owndetails-bodyrow <?=$numpp%2 == 0 ? 'greydatarow' : 'whitedatarow'?> <?=$own['datclass']?>">
                    <div class="accreceiv-owndetails-bodynum"><?=$numpp?></div>
                    <div class="accreceiv-owndetails-bodyapproval <?=$own['order_blank']==1 ? '' : ($own['approved']==0 ? 'notapproved' : '')?>"><?=$own['order_blank']==1 ? 'Blank' : ($own['approved']==0 ? 'Not Approved' : 'Approved')?></div>
                    <div class="accreceiv-owndetails-bodytype <?=$own['typeclass']?>"><?=$rowtype?></div>
                    <div class="accreceiv-owndetails-bodyrunningtotal"><?=MoneyOutput($own['rundebt'],0)?></div>
                    <div class="accreceiv-owndetails-bodydue <?=$own['dueclass']?>"><?=date('m/d/y', $own['batch_due'])?></div>
                    <div class="accreceiv-owndetails-bodydays <?=$own['dayclass']?>">
                        <?php if ($ownsort=='owntype') : ?>
                            <?=$own['daysshow']?>
                        <?php else : ?>
                            <?=$own['days']?>
                        <?php endif; ?>
                    </div>
                    <div class="accreceiv-owndetails-bodybalance"><?=TotalOutput($own['balance'],1)?></div>
                    <div class="accreceiv-owndetails-bodybrand <?=$own['brand']=='SR' ? 'stressrelievers' : 'bluetrack'?>"><?=$own['brand']?></div>
                    <div class="accreceiv-owndetails-bodyordersigma" data-brand="<?=$own['brand']?>" data-order="<?=$own['order_id']?>"><?=$own['order_num']?></div>
<!--                    <div class="accreceiv-owndetails-bodyponumber" title="--><?php //=$own['customer_ponum']?><!--">--><?php //=$own['customer_ponum']?><!--</div>-->
                    <div class="accreceiv-owndetails-bodycustomer"><?=$own['customer_name']?></div>
                    <div class="accreceiv-owndetails-bodystatus" data-order="<?=$own['order_id']?>">
                        <?php if (!empty($own['debt_status'])) : ?>
                            <div class="accreceiv-statusbtn active" data-order="<?=$own['order_id']?>">
                                <i class="fa fa-file-text-o"></i>
                            </div>
                            <div class="accreceiv-statusdate"><?=date('m/d', $own['update_date'])?> - </div>
                            <div class="accreceiv-statustext"><?=$own['debt_status']?></div>
                        <?php else : ?>
                            <div class="accreceiv-statusbtn" data-order="<?=$own['order_id']?>">
                                <i class="fa fa-file-text-o"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="accreceiv-owndetails-bodystatusedit" data-order="<?=$own['order_id']?>"></div>
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
            <?php $startyear = 0; ?>
            <?php foreach ($refunds as $refund) { ?>
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
                    <div class="accreceiv-refunddetails-bodybrand <?=$refund['brand']=='SR' ? 'stressrelievers' : 'bluetrack'?>"><?=$refund['brand']?></div>
                    <div class="accreceiv-refunddetails-bodyordersigma" data-brand="<?=$refund['brand']?>" data-order="<?=$refund['order_id']?>"><?=$refund['order_num']?></div>
                    <div class="accreceiv-refunddetails-bodycustomer"><?=$refund['customer_name']?></div>
                </div>
                <?php $numpp++; ?>
            <?php } ?>
        <?php } ?>
    </div>

</div>
