<div class="accreceiv-totals">
    <div class="accreceiv-totals-title">Owed to Us:</div>
    <div class="accreceiv-totals-value"><?=TotalOutput($totalown)?></div>
</div>
<div class="accreceiv-totals">
    <div class="accreceiv-totals-title">Refunds Owed:</div>
    <div class="accreceiv-totals-value refund">(<?=TotalOutput(abs($totalrefund))?>)</div>
</div>
<div class="accreceiv-totals">
    <div class="accreceiv-totals-title totalar">TOTAL AR:</div>
    <div class="accreceiv-totals-value totalbalance <?=$balance < 0 ? 'refund' : ''?>"><?=$balance < 0 ? '('.TotalOutput(abs($balance)).')' : TotalOutput($balance)?></div>
</div>
