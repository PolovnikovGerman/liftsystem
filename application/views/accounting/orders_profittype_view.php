<div class="order_profit_typedat <?=$totals['type']?>">
    <div class="order_profit_title">
        <div class="head">
            <div class="year">Year</div>
            <div class="orders">Total Orders</div>
            <div class="revenue">Total Revenue</div>
            <div class="profit">Total Profit</div>
            <div class="avgrevenue">Avg Revenue</div>
            <div class="avgprofit">Avg Profit</div>
        </div>
        <div class="data">
            <div class="year"><?=$totals['order_date']?></div>
            <div class="orders"><?=$totals['total_orders']?></div>
            <div class="revenue"><?=$totals['total_revenue']?></div>
            <div class="profit"><?=$totals['total_profit']?></div>
            <div class="avgrevenue"><?=$totals['avg_revenue']?></div>
            <div class="avgprofit"><?=$totals['avg_profit']?></div>
        </div>
    </div>
    <div class="order_profit_datahead">
        <div class="order_profit_ordnum">Order #</div>
        <div class="order_profit_orddate">Date</div>
        <div class="order_profit_customer">Customer</div>
        <div class="order_profit_itemname">Item</div>
        <div class="order_profit_ordsums">Revenue</div>
        <div class="order_profit_ordsums">Profit</div>
        <div class="order_profit_ordperc">Prof %</div>
    </div>
    <div class="order_profit_data">
        <?php foreach ($orders as $row) {?>
        <div class="order_profit_datarow">
            <div class="order_profit_ordnum"><?=$row['order_num']?></div>
            <div class="order_profit_orddate"><?=$row['order_date']?></div>
            <div class="order_profit_customer"><?=$row['customer']?></div>
            <div class="order_profit_itemname"><?=$row['item_name']?></div>
            <div class="order_profit_ordsums"><?=$row['revenue']?></div>
            <div class="order_profit_ordsums"><?=$row['profit']?></div>
            <div class="order_profit_ordperc"><?=$row['profit_perc']?></div>
        </div>
        <?php } ?>
    </div>
</div>