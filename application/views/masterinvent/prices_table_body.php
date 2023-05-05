<?php $numrow = 0; ?>
<?php foreach ($lists as $list) { ?>
    <div class="inventoryprice_table_row <?= $numrow % 2 == 0 ? 'greydatarow' : 'whitedatarow' ?> <?=$list['rowclass']?>">
        <div class="inventoryincome_date"><?= date('m/d/y', $list['income_date']) ?></div>
        <div class="inventoryincome_recnum"><?=$list['income_record']?></div>
        <div class="inventoryincome_descript"><?= $list['income_description'] ?></div>
        <div class="inventoryincome_price"><?= $list['income_price'] ?></div>
        <div class="inventoryincome_incomeqty"><?= QTYOutput($list['income_qty']) ?></div>
        <div class="inventoryincome_divide">&nbsp;</div>
        <div class="inventoryincome_leftqty"><?= QTYOutput($list['income_left']) ?></div>
        <div class="inventoryincome_lefttotal"><?=MoneyOutput($list['income_left_total'])?></div>
    </div>
    <?php $numrow++; ?>
<?php } ?>
