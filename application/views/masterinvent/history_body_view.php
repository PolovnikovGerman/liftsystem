<div class="inventoryhistory_body_content">
    <input type="hidden" id="invenorynewhistoryadd" value="0"/>
    <div class="inventoryhistory_table_head">
        <div class="instock_date">
            <span class="outcomelistadd" data-item="<?=$item['inventory_color_id']?>"><i class="fa fa-plus-circle" aria-hidden="true"></i></span>
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
                <div class="instock_recnum" data-rectype="<?=$list['rectype']?>" data-order="<?=$list['order']?>">
                    <?=$list['record']?>
                </div>
                <div class="instock_descript"><?=$list['description'] ?></div>
                <div class="instock_amount <?=$list['type']=='O' ? 'negative' : ''?>">
                    <?=$list['type']=='O' ? '(' : ''?><?=QTYOutput($list['amount'])?><?=$list['type']=='O' ? ')' : '' ?>
                </div>
                <div class="instock_balance <?=$list['balance']<0 ? 'negative' : ''?>">
                    <?=$list['balance']<0 ? '(' : ''?><?=QTYOutput(abs($list['balance']))?><?=$list['balance']<0 ? ')' : ''?>
                </div>
            </div>
            <?php $numrow++; ?>
        <?php } ?>
    </div>
<!--    <div class="datarow">-->
<!--        <div class="inventoryhistory_view_prices" data-item="--><?php //=$item['inventory_color_id']?><!--">[View Price History]</div>-->
<!--    </div>-->
</div>