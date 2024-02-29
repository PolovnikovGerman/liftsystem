<div class="accreceiv-content-left <?=$brand=='ALL' ? 'sigmasystem' : ''?>">
    <div class="accreceiv-totalown">
        <div class="accreceiv-totalown-title">Owed to Us:</div>
        <div class="accreceiv-totalown-value"><?=TotalOutput($totalown)?></div>
    </div>
    <div class="accreceiv-totalpast">
        <div class="accreceiv-totalpast-title">Past Due:</div>
        <div class="accreceiv-totalpast-value"><?=TotalOutput($pastown)?></div>
    </div>
    <div class="accreceiv-totalown-table">
        <?php foreach ($own as $row) { ?>
            <div class="accreceiv-totalown-cell">
                <div class="accreceiv-totalown-cell-year"><?=$row['year']?></div>
                <div class="accreceiv-totalown-cell-value"><?=$row['balance']==0 ? '---' : TotalOutput($row['balance'])?></div>
            </div>
        <?php } ?>
    </div>
</div>
<div class="accreceiv-content-center <?=$brand=='ALL' ? 'sigmasystem' : ''?>">
    <div class="accreceiv-totalrefund">
        <div class="accreceiv-totalrefund-title">Refunds to Customers:</div>
        <div class="accreceiv-totalrefund-value">(<?=TotalOutput(abs($totalrefund))?>)</div>
    </div>
    <div class="accreceiv-totalrefund-table">
        <?php foreach ($refund as $row) { ?>
            <div class="accreceiv-totalrefund-cell">
                <div class="accreceiv-totalrefund-cell-year"><?=$row['year']?></div>
                <div class="accreceiv-totalrefund-cell-value <?=$row['balance']==0 ? 'empty' : ''?>"><?=$row['balance']==0 ? '---' : '('.TotalOutput(abs($row['balance'])).')'?></div>
            </div>
        <?php } ?>
    </div>
</div>
