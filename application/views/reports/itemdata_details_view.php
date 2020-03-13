<div class="salesitemdetailsarea">
    <div class="title"><?=$title?></div>
    <div class="datahead">
        <div class="date">Date</div>
        <div class="ordernum">Order #</div>
        <div class="orderqty">QTY</div>
        <div class="customer">Customer</div>
        <div class="profit">Profit</div>
        <div class="revenue">Revenue</div>
        <div class="profit_perc">%</div>
    </div>
    <div class="dataarea">
        <?php $nrow=0;?>
        <?php foreach ($data as $row) {?>
            <div class="datarow <?=$row['rowclass']?> <?=($nrow%2==0 ? 'white' : 'grey')?>">
                <div class="date"><?=$row['order_date']?></div>
                <div class="ordernum"><?=$row['order_num']?></div>
                <div class="orderqty"><?=$row['order_qty']?></div>
                <div class="customer"><?=$row['customer_name']?></div>
                <div class="profit"><?=$row['out_profit']?></div>
                <div class="revenue"><?=$row['out_revenue']?></div>
                <div class="profit_perc <?=$row['profit_class']?>"><?=$row['profit_perc']?></div>
            </div>
            <?php $nrow++?>
        <?php } ?>
    </div>
    <div class="datatotals">
        <div class="total">TOTAL:</div>
        <div class="numorders"><?=$totals['numorders']?></div>
        <div class="totalqty"><?=$totals['qty']?></div>
        <div class="profit"><?=$totals['out_profit']?></div>
        <div class="revenue"><?=$totals['out_revenue']?></div>
        <div class="profit_perc <?=$totals['profit_class']?>"><?=$totals['profit_perc']?></div>
    </div>
</div>