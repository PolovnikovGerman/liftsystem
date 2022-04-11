<div class="row accreceiv-totals">
    <div class="col-7 accreceiv-totals-title">Owed to Us:</div>
    <div class="col-5 accreceiv-totals-value"><?=TotalOutput($totalown)?></div>
</div>
<div class="row accreceiv-totals">
    <div class="col-7 accreceiv-totals-title">Refunds Owed:</div>
    <div class="col-5 accreceiv-totals-value refund">(<?=TotalOutput(abs($totalrefund))?>)</div>
</div>
<div class="row accreceiv-totals">
    <div class="col-7 accreceiv-totals-title totalar">TOTAL AR:</div>
    <div class="col-5 accreceiv-totals-value totalbalance <?=$balance < 0 ? 'refund' : ''?>"><?=$balance < 0 ? '('.TotalOutput(abs($balance)).')' : TotalOutput($balance)?></div>
</div>
