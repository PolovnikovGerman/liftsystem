<div class="orderreportprofitview <?=$order['profit_class']?>">
    <div class="labeltxt">Profit</div>
    <div class="value"><?=  MoneyOutput($order['profit'])?></div>
    <div class="value"><?=round($order['profit_perc'],0)?>%</div>
    <div class="inventorylevel">
        <div class="instock"><?=QTYOutput($invent['instock'])?></div>
        <div class="output"><?=QTYOutput($invent['outcome'])?></div>
        <div class="balance"><?=QTYOutput($invent['balance'])?></div>
    </div>
</div>