<div class="orders_by_date">
    <div class="orders_by_date_title">Orders <?=date('m/d/Y',$date)?></div>
    <div class="odrdate_tablehead">
        <div class="orddate_ordernum">Order #</div>
        <div class="orddate_customer">Customer</div>
        <div class="orddate_qty">QTY</div>
        <div class="orddate_item">Item</div>
        <div class="orddate_revenue">Revenue</div>
        <div class="orddate_profit">Profit</div>
        <div class="orddate_profitperc">%</div>
        <div class="orddate_status">Status</div>
    </div>
    <div class="orddate_tabledat">
        <?php $nrow=0;?>
        <?php foreach ($orders as $row) {?>
        <div class="odrdate_tablerow <?=$row['item_class']?> <?=($nrow%2 ? '' : 'grey')?>">
            <div class="orddate_ordernumdat"><?=$row['order_num']?></div>
            <div class="orddate_customerdat"><?=$row['customer_name']?></div>
            <div class="orddate_qtydat"><?=$row['order_qty']?></div>
            <div class="orddate_itemdat"><?=$row['order_items']?></div>
            <div class="orddate_revenuedat"><?=$row['revenue']?></div>
            <div class="orddate_profitdat <?=$row['profit_class']?>"><?=$row['profit']?></div>
            <div class="orddate_profitpercdat <?=$row['profit_class']?>"><?=$row['profit_perc']?></div>
            <div class="orddate_statusdat"><?=$row['status']?></div>
        </div>
        <?php $nrow++;?>
        <?php } ?>

    </div>
</div>