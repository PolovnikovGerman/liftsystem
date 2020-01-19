<?php if (count($item_dat) == 0) { ?>
    <div class="whitedatarow">
        <div class="emptyprofitdata">No records</div>
    </div>
<?php } else { ?>
    <?php $n_row = 1 + $offset; ?>
    <?php foreach ($item_dat as $row) { ?>
        <div class="<?= ($n_row % 2 == 0 ? 'greydatarow' : 'whitedatarow') ?> profittablerow">
            <div class="profitdatacell numinlist"><?= $n_row ?></div>
            <div class="profitdatacell editcoll" data-item="<?=$row['item_id']?>"><i class="fa fa-pencil-square-o edit_item" aria-hidden="true"></i></div>
            <div class="profitdatacell itemnum"><?= $row['item_number'] ?></div>
            <div class="profitdatacell overflowtext itemtitle <?= $row['itemnameclass'] ?>" data-content="<?=$row['item_name']?>">
                <?= $row['item_name'] ?>
            </div>
            <div class="profitdatacell vendorcost"><?= ($row['vendor_item_cost'] == '' ? '' : '$' . $row['vendor_item_cost']) ?></div>
            <div class="profitdatacell vendorname"><?= $row['vendor_name'] ?></div>
            <div class="profitdatacell priceseparator">&nbsp;</div>
            <div class="profitdatacell pricedatacell <?= $row['profit_25_class'] ?>">
                <?= ($row['profit_25_class'] == 'empty' ? 'n/a' : $row['profit_25'] . ' %') ?>
            </div>
            <div class="profitdatacell pricedatacell <?= $row['profit_75_class'] ?>">
                <?= ($row['profit_75_class'] == 'empty' ? 'n/a' : $row['profit_75'] . ' %') ?>
            </div>
            <div class="profitdatacell pricedatacell <?= $row['profit_150_class'] ?>">
                <?= ($row['profit_150_class'] == 'empty' ? 'n/a' : $row['profit_150'] . ' %') ?>
            </div>
            <div class="profitdatacell pricedatacell <?= $row['profit_250_class'] ?>">
                <?= ($row['profit_250_class'] == 'empty' ? 'n/a' : $row['profit_250'] . ' %') ?>
            </div>
            <div class="profitdatacell pricedatacell <?= $row['profit_500_class'] ?>">
                <?= ($row['profit_500_class'] == 'empty' ? 'n/a' : $row['profit_500'] . ' %') ?>
            </div>
            <div class="profitdatacell pricedatacell <?= $row['profit_1000_class'] ?>">
                <?= ($row['profit_1000_class'] == 'empty' ? 'n/a' : $row['profit_1000'] . ' %') ?>
            </div>
            <div class="profitdatacell pricedatacell <?= $row['profit_2500_class'] ?>">
                <?= ($row['profit_2500_class'] == 'empty' ? 'n/a' : $row['profit_2500'] . ' %') ?>
            </div>
            <div class="profitdatacell pricedatacell <?= $row['profit_5000_class'] ?>">
                <?= ($row['profit_5000_class'] == 'empty' ? 'n/a' : $row['profit_5000'] . ' %') ?>
            </div>
            <div class="profitdatacell pricedatacell <?= $row['profit_10000_class'] ?>">
                <?= ($row['profit_10000_class'] == 'empty' ? 'n/a' : $row['profit_10000'] . ' %') ?>
            </div>
            <div class="profitdatacell pricedatacell <?= $row['profit_20000_class'] ?>">
                <?= ($row['profit_20000_class'] == 'empty' ? 'n/a' : $row['profit_20000'] . ' %') ?>
            </div>
            <div class="profitdatacell emptyspace">&nbsp;</div>
            <div class="profitdatacell pricespecdatacell <?= $row['profit_print_class'] ?>">
                <?= ($row['profit_print_class'] == 'empty' ? 'n/a' : $row['profit_print'] . ' %') ?>
            </div>
            <div class="profitdatacell pricespecdatacell <?= $row['profit_setup_class'] ?>">
                <?= ($row['profit_setup_class'] == 'empty' ? 'n/a' : $row['profit_setup'] . ' %') ?>
            </div>
        </div>
        <?php $n_row++; ?>
    <?php } ?>
<?php } ?>
