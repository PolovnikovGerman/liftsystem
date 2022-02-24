<div class="row">
    <div class="col-12 accreceiv-owndetails-head">
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
        <?php if ($ownsort=='order_num') { ?>
            <div class="accreceiv-owndetails-headorder ownsort" data-sort="order_num">Order <span><i class="fa <?=$owndir=='desc' ? 'fa-sort-amount-desc' : 'fa-sort-amount-asc'?>"></i></span></div>
        <?php } else { ?>
            <div class="accreceiv-owndetails-headorder ownsort" data-sort="order_num">Order <span></span></div>
        <?php } ?>
        <div class="accreceiv-owndetails-headcustomer">Customer</div>
        <div class="accreceiv-owndetails-headtype">Type</div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="accreceiv-owndetails-body">
            <?php if (count($owns)==0) { ?>
                <div class="accreceiv-owndetails-bodyrow empty">No orders</div>
            <?php } else { ?>
                <?php $numpp=1;?>
                <?php foreach ($owns as $own) { ?>
                    <div class="accreceiv-owndetails-bodyrow <?=$numpp%2 == 0 ? 'greydatarow' : 'whitedatarow'?>">
                        <div class="accreceiv-owndetails-bodynum"><?=$numpp?></div>
                        <div class="accreceiv-owndetails-bodydue <?=$own['batch_due'] < $daystart ? 'pastdue' : ''?>">
                            <?=date('m/d/y', $own['batch_due'])?>
                        </div>
                        <div class="accreceiv-owndetails-bodybalance"><?=TotalOutput($own['balance'])?></div>
                        <div class="accreceiv-owndetails-bodyorder" data-order="<?=$own['order_id']?>"><?=$own['order_num']?></div>
                        <div class="accreceiv-owndetails-bodycustomer"><?=$own['customer_name']?></div>
                        <div class="accreceiv-owndetails-bodytype <?=$own['typeclass']?>"><?=$own['type']?></div>
                    </div>
                    <?php $numpp++;?>
                <?php } ?>
            <?php } ?>
        </div>
    </div>
</div>