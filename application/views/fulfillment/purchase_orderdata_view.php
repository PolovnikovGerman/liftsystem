<div class="purchaseorder-row">
    <div class="poorder_label">Order #</div>
    <div class="poorder_label"><?=$order_num?></div>
    <div class="poorder_label">Date </div>
    <div class="poorder_label"><?=date('m/d/y', $order_date)?></div>
    <div class="poorder_label">Profit </div>
    <div class="poprofit-data <?=$profit_class?>"><?=$profit?></div>
    <span style="width:1%">&nbsp;</span>
    <div class="poprofitperc <?=$profit_class?>"><?=($profit_perc=='' ? 'Proj' : $profit_perc.'%')?></div>
</div>
