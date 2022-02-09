<div class="accreceiv-content-left">
    <div class="accreceiv-totalown">
        <div class="accreceiv-totalown-title">Owed to Us:</div>
        <div class="accreceiv-totalown-value"><?=MoneyOutput($totalown)?></div>
    </div>
    <div class="accreceiv-totalpast">
        <div class="accreceiv-totalpast-title">Past Due:</div>
        <div class="accreceiv-totalpast-value"><?=MoneyOutput($pastown)?></div>
    </div>
    <div class="accreceiv-totalown-table">
        <?php foreach ($own as $row) { ?>
            <div class="accreceiv-totalown-cell">
                <div class="accreceiv-totalown-cell-year"><?=$row['year']?></div>
                <div class="accreceiv-totalown-cell-value"><?=$row['balance']==0 ? '---' : MoneyOutput($row['balance'])?></div>
            </div>
        <?php } ?>
    </div>
</div>
<div class="accreceiv-content-center">
    <div class="accreceiv-totalrefund">
        <div class="accreceiv-totalrefund-title">Refunds to Customers:</div>
        <div class="accreceiv-totalrefund-value">(<?=MoneyOutput(abs($totalrefund))?>)</div>
    </div>
    <div class="accreceiv-totalrefund-table">
        <?php foreach ($refund as $row) { ?>
            <div class="accreceiv-totalrefund-cell">
                <div class="accreceiv-totalrefund-cell-year"><?=$row['year']?></div>
                <div class="accreceiv-totalrefund-cell-value <?=$row['balance']==0 ? 'empty' : ''?>"><?=$row['balance']==0 ? '---' : '('.MoneyOutput(abs($row['balance'])).')'?></div>
            </div>
        <?php } ?>
    </div>
</div>
