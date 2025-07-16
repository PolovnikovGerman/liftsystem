<div class="inventorydetails_body_content">
    <input type="hidden" id="invenorynewhistoryadd" value="0"/>
    <div class="inventorydetails_table_title">Inventory Used:</div>
    <div class="inventorydetails_table_head">
        <div class="instock_date">
            <span class="outcomelistadd" data-item="<?=$item['inventory_color_id']?>"><i class="fa fa-plus-circle" aria-hidden="true"></i></span>
            Date
        </div>
        <div class="instock_recnum">Record #</div>
        <div class="instock_descript">Description</div>
        <div class="instock_amount">Amnt</div>
        <div class="instock_balance">Balance</div>
    </div>
    <div class="inventorydetails_table_body">
        <?php $numrow = 0; ?>
        <?php foreach ($lists as $list) { ?>
            <div class="inventorydetails_table_row <?= $numrow % 2 == 0 ? 'greydatarow' : 'whitedatarow' ?>">
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
    <div class="datarow">
        <div class="inventorydetails_total">In Stock:</div>
        <div class="inventorydetails_total_stock"><?=QTYOutput($balance)?></div>
    </div>
    <div class="datarow">
        <div class="inventorydetails_reseved_title">Reserved:</div>
    </div>
    <div class="datarow">
        <div class="inventorydetails_reseved_head">&nbsp;</div>
    </div>
    <div class="datarow">
        <div class="inventorydetails_reseved_body">&nbsp;</div>
    </div>
    <div class="datarow">
        <div class="inventorydetails_reserved">Available:</div>
        <div class="inventorydetails_total_reserved"><?=QTYOutput($balance)?></div>
    </div>

</div>