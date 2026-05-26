<?php if (count($ownnotapproved)==0) : ?>
    <div class="accreceiv-owndetails-bodyrow empty">No orders</div>
<?php else : ?>
    <?php $numpp = 1; ?>
    <?php $curtype = '' ?>
    <?php $dayclass = '' ?>
    <?php foreach($ownnotapproved as $own) : ?>
        <?php if ($ownsort=='batch_due') : ?>
            <?php if ($own['dayclass']!=$dayclass) : ?>
                <?php $dayclass = $own['dayclass']; ?>
                <?php if ($dayclass=='pastdue') : ?>
                    <div class="accreceiv-owndetails-bodyrowdayseparator"> Due Now </div>
                <?php else : ?>
                    <div class="accreceiv-owndetails-bodyrowdayseparator"> Not Due Yet </div>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
        <div class="accreceiv-owndetails-bodyrow <?=$numpp%2 == 0 ? 'greydatarow' : 'whitedatarow'?> <?=$own['datclass']?>">
            <?php if ($ownsort=='owntype') : ?>
                <?php if ($own['type']!==$curtype) : ?>
                    <?php $rowtype = $own['type']; $curtype = $own['type']; ?>
                <?php else: ?>
                    <?php $rowtype = '----'; ?>
                <?php endif; ?>
            <?php else : ?>
                <?php $rowtype = $own['type']; ?>
            <?php endif; ?>
            <div class="accreceiv-owndetails-bodynum"><?=$numpp?></div>
            <div class="accreceiv-owndetails-bodyapproval notapproved">Not Approved</div>
            <div class="accreceiv-owndetails-bodyrunningtotal"><?=MoneyOutput($own['rundebt'],0)?></div>
            <div class="accreceiv-owndetails-bodydays <?=$own['dayclass']?>">
                <?=$own['days']?>
            </div>
            <div class="accreceiv-owndetails-bodydue <?=$own['dueclass']?>">
                <?=date('m/d/y', $own['batch_due'])?>
            </div>
            <div class="accreceiv-owndetails-bodytype <?=$own['typeclass']?>"><?=$rowtype?></div>
            <div class="accreceiv-owndetails-bodybalance"><?=TotalOutput($own['balance'], 1)?></div>
            <?php if ($brand=='all') : ?>
                <div class="accreceiv-owndetails-bodybrand <?=$own['brand']=='SR' ? 'stressrelievers' : 'bluetrack'?>"><?=$own['brand']?></div>
            <?php endif; ?>
            <div class="accreceiv-owndetails-bodyorder" data-order="<?=$own['order_id']?>"><?=$own['order_num']?></div>
            <div class="accreceiv-owndetails-bodyconfirm"><?=$own['order_confirm']?></div>
            <?php if ($brand=='sr') : ?>
                <div class="accreceiv-owndetails-bodyponumber" title="<?=$own['customer_ponum']?>"><?=$own['customer_ponum']?></div>
            <?php endif; ?>
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
<!--            <div class="accreceiv-owndetails-bodystatusedit" data-order="--><?php //=$own['order_id']?><!--"></div>-->
        </div>
        <?php $numpp++; ?>
    <?php endforeach; ?>
<?php endif; ?>