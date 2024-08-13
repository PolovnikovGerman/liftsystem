<?= $dateview ?>
<div class="dateblock">
    <div class="stock-plates-box">
        <div class="stock-box">
            <h4>STOCK TO GET:</h4>
            <div class="stock-table">
                <div class="stock-table-tr stock-table-header">
                    <div class="stock-table-td-move">&nbsp;</div>
                    <div class="stock-table-td-done">&nbsp;</div>
                    <div class="stock-table-td-order">Order#</div>
                    <div class="stock-table-td-qty">Qty</div>
                    <div class="stock-table-td-itemcolor">Item Color/s</div>
                    <div class="stock-table-td-descriptions">Item / Description</div>
                </div>
                <?= $stockview ?>
            </div>
        </div>
        <div class="plates-box">
            <h4>PLATES TO MAKE:</h4>
            <div class="plates-table">
                <div class="plates-table-tr plates-table-header">
                    <div class="plates-table-td-move">&nbsp;</div>
                    <div class="plates-table-td-done">&nbsp;</div>
                    <div class="plates-table-td-order">Order#</div>
                    <div class="plates-table-td-plates">Qty</div>
                    <div class="plates-table-td-descriptions">Item / Description</div>
                </div>
                <?= $plateview ?>
            </div>
        </div>
    </div>
    <div class="ready-print-block">
        <?=$printview?>
    </div>
</div>
