<div class="inventoryprice_body_content">
    <div class="inventoryprice_table_head">
        <div class="inventoryincome_date">
            <span class="incomelistadd"><i class="fa fa-plus-circle" aria-hidden="true"></i></span>
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
    <div class="inventoryprice_table_body">
        <?php $numrow = 0; ?>
        <?php foreach ($lists as $list) { ?>
            <div class="inventoryprice_table_row <?= $numrow % 2 == 0 ? 'greydatarow' : 'whitedatarow' ?>">
                <div class="inventoryincome_date"><?= date('m/d/y', $list['income_date']) ?></div>
                <div class="inventoryincome_recnum"><?=$list['income_record']?></div>
                <div class="inventoryincome_descript"><?= $list['income_description'] ?></div>
                <div class="inventoryincome_price"><?= $list['income_price'] ?></div>
                <div class="inventoryincome_incomeqty"><?= QTYOutput($list['income_qty']) ?></div>
                <div class="inventoryincome_divide">&nbsp;</div>
                <div class="inventoryincome_leftqty"><?= QTYOutput($list['income_left']) ?></div>
                <div class="inventoryincome_lefttotal"><?=MoneyOutput($list['income_left_total'])?></M></div>
            </div>
            <?php $numrow++; ?>
        <?php } ?>
    </div>
    <div class="inventoryprice_total_left">
        <div class="viewbalancesused">+ view balances used</div>
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
