<div class="inventoryhistory_body_content">
    <div class="inventoryhistory_table_head">
        <div class="instock_date">
            <span class="incomelistadd"><i class="fa fa-plus-circle" aria-hidden="true"></i></span>
            Date
        </div>
        <div class="instock_recnum">Record #</div>
        <div class="instock_descript">Description</div>
        <div class="instock_amount">Amnt</div>
        <div class="instock_balance">Balance</div>
    </div>
    <div class="inventoryhistory_table_body">
        <?php $numrow = 0; ?>
        <?php foreach ($lists as $list) { ?>
            <div class="inventoryhistory_table_row <?= $numrow % 2 == 0 ? 'greydatarow' : 'whitedatarow' ?>">
                <div class="instock_date"><?= date('m/d/y', $list['date']) ?></div>
                <div class="instock_recnum"><?=$list['record']?></div>
                <div class="instock_descript"><?=$list['description'] ?></div>
                <div class="instock_amount <?=$list['type']=='O' ? 'negative' : ''?>">
                    <?=$list['type']=='O' ? '(' : ''?><?=QTYOutput($list['amount'])?><?=$list['type']=='O' ? ')' : '' ?>
                </div>
                <div class="instock_balance"><?=QTYOutput($list['balance'])?></M></div>
            </div>
            <?php $numrow++; ?>
        <?php } ?>
    </div>
    <div class="datarow">
        <div class="inventoryhistory_view_prices" data-item="<?=$item['inventory_color_id']?>">[View Price History]</div>
    </div>
</div>