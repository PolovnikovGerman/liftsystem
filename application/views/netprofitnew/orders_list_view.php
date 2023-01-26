<div class="orderslistarea">
    <div class="orderslist_title">Orders in PROJ stage from <?=date('m/d/Y',$profit['datebgn'])?> to <?=date('m/d/Y',$profit['dateend'])?></div>
    <div class="orderlistdata_head">
        <div class="orderlistdata_orderdate">Date</div>
        <div class="orderlistdata_ordernum">Order #</div>
        <div class="orderlistdata_customer">Customer</div>
        <div class="orderlistdata_qty">QTY</div>
        <div class="orderlistdata_item">Item</div>
        <div class="orderlistdata_revenue">Revenue</div>
        <div class="orderlistdata_profdat">PROJ Profit</div>
        <div class="orderlistdata_artstate">Stage</div>
        <div class="orderlistdata_artstatediff">Time in Stage</div>
    </div>
    <div class="orderlistdata">
        <?php $nrow=0;?>
        <?php foreach ($list as $row) {?>
        <div class="orderlistdata_row <?=($nrow%2==0 ?'white' : 'grey')?>">
            <div class="orderlistdata_orderdate_cell"><?=$row['out_date']?></div>
            <div class="orderlistdata_ordernum_cell"><?=$row['order_num']?></div>
            <div class="orderlistdata_customer_cell"><?=$row['customer_name']?></div>
            <div class="orderlistdata_qtytotal_cell"><?=$row['order_qty']?></div>
            <div class="orderlistdata_item_cell <?=$row['item_class']?>"><?=$row['order_items']?></div>
            <div class="orderlistdata_revenue_cell"><?=$row['out_revenue']?></div>
            <div class="orderlistdata_profdat_cell"><?=$row['out_profit']?></div>
            <div class="orderlistdata_artstate_cell"><?=$row['art_stage']?></div>
            <div class="orderlistdata_artstatediff_cell"><?=$row['diff']?></div>
        </div>
        <?php $nrow++?>
        <?php } ?>
    </div>
    <div class="orderlistdata_total">
        <div class="orderlistdata_orderdate_cell">Total</div>
        <div class="orderlistdata_ordernum_cell"><?=$totals['numorders']?></div>
        <div class="orderlistdata_customer_cell"><?=$totals['out_customer']?></div>
        <div class="orderlistdata_qtytotal_cell"><?=$totals['all_qty']?></div>
        <div class="orderlistdata_item"><?=$totals['out_item']?></div>
        <div class="orderlistdata_revenue_cell"><?=$totals['out_revenue']?></div>
        <div class="orderlistdata_profdat_cell"><?=$totals['out_profit']?></div>
        <div class="orderlistdata_artstage_cell"><?=$totals['out_artstage']?></div>        
    </div>
</div>
