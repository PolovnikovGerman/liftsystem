<div class="w9purchaseexpensesarea">
    <div class="title"><?=$category?> (<?= $type ?>)</div>
    <div class="tablehead">
        <div class="week">Week</div>
        <div class="amount">Amount</div>
        <div class="vendor">Vendor</div>
        <div class="description">Description</div>        
    </div>
    <div class="tablebody">
        <?php $nrow = 0; ?>
        <?php foreach ($data as $row) { ?>
            <div class="tabledatarow <?= ($nrow % 2 == 0 ? 'grey' : 'white') ?>">
                <div class="week"><?= $row['week'] ?></div>
                <div class="amount <?= $row['amount_class'] ?>"><?= $row['amount'] ?></div>
                <div class="vendor"><?= $row['vendor'] ?></div>
                <div class="description"><?= $row['description'] ?></div>
            </div>
            <?php $nrow++; ?>
        <?php } ?>
    </div>
    <div class="totals">
        <div class="label">Total</div>
        <div class="value"><?=MoneyOutput($totals,2)?></div>
    </div>
</div>