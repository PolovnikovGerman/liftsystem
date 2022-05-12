<input type="hidden" id="priceslistshowused" value="0"/>
<div class="inventoryprice_body_content">
    <div class="inventoryprice_table_head">
        <div class="inventoryincome_date">
            <span class="incomelistadd" data-item="<?=$item['inventory_color_id']?>"><i class="fa fa-plus-circle" aria-hidden="true"></i></span>
            Date
        </div>
        <div class="inventoryincome_recnum">Record #</div>
        <div class="inventoryincome_descript">Description</div>
        <div class="inventoryincome_price">Price Ea</div>
        <div class="inventoryincome_incomeqty">Orig. Qty</div>
        <div class="inventoryincome_divide">&nbsp;</div>
        <div class="inventoryincome_leftqty">Qty Left</div>
        <div class="inventoryincome_lefttotal">Value Left</div>
    </div>
    <div class="inventoryprice_table_body"><?=$tablebody?></div>
    <div class="inventoryprice_total_left">
        <div class="viewbalancesused" data-item="<?=$item['inventory_color_id']?>">+ view balances used</div>
    </div>
    <div class="inventoryprice_total_right">
        <div class="datarow">
            <div class="inventoryprice_total_label">Inventory Balance:</div>
            <div class="inventoryprice_total_value"><?= empty($totals['balance_qty']) ? '' : QTYOutput($totals['balance_qty']) . 'pc' ?></div>
        </div>
        <div class="datarow">
            <div class="inventoryprice_total_label">Total Value of Balance:</div>
            <div class="inventoryprice_total_value"><?= MoneyOutput($totals['balance_total']) ?></div>
        </div>
        <div class="datarow">
            <div class="inventoryprice_total_label">Average Price:</div>
            <div class="inventoryprice_total_value"><?= MoneyOutput($totals['avg_price'], 3) ?></div>
        </div>
    </div>
</div>
