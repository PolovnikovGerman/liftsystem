<div class="row">
    <div class="col-12 accreceiv-totalrefund">
        <div class="row">
            <div class="col-6 col-sm-5 col-md-5 col-lg-6 accreceiv-totalrefund-title">Refunds to Customers:</div>
            <div class="col-5 col-sm-5 col-md-4 col-lg-4 accreceiv-totalrefund-value">(<?=TotalOutput(abs($totalrefund))?>)</div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12 accreceiv-totalrefund-table">
        <?php foreach ($refund as $row) { ?>
            <div class="accreceiv-totalrefund-cell">
                <div class="accreceiv-totalrefund-cell-year"><?=$row['year']?></div>
                <div class="accreceiv-totalrefund-cell-value <?=$row['balance']==0 ? 'empty' : ''?>"><?=$row['balance']==0 ? '---' : '('.TotalOutput(abs($row['balance'])).')'?></div>
            </div>
        <?php } ?>
    </div>
</div>
