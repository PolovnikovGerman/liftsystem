<div class="accreceiv-totals">
    <div class="accreceiv-totals-title">Owed to Us:</div>
    <div class="accreceiv-totals-value"><?=MoneyOutput($totalown)?></div>
</div>
<div class="accreceiv-totals">
    <div class="accreceiv-totals-title">Refunds Owed:</div>
    <div class="accreceiv-totals-value refund">(<?=MoneyOutput(abs($totalrefund))?>)</div>
</div>
<div class="accreceiv-totals">
    <div class="accreceiv-totals-title">TOTAL AR:</div>
    <div class="accreceiv-totals-value totalbalance <?=$balance < 0 ? 'refund' : ''?>"><?=$balance < 0 ? '('.MoneyOutput(abs($balance)).')' : MoneyOutput($balance)?></div>
</div>
