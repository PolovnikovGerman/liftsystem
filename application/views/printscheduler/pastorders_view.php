<?php if ($numorders > 0) { ?>
    <div class="pastdue-table">
        <div class="pdo-table-tr pdo-table-header">
            <div class="pdo-table-td-move">&nbsp;</div>
            <div class="pdo-table-td-icons">&nbsp;</div>
            <div class="pdo-table-td-ship">Ship</div>
            <div class="pdo-table-td-order">Order#</div>
            <div class="pdo-table-td-items">#Items</div>
            <div class="pdo-table-td-imp">Imp</div>
            <div class="pdo-table-td-prints">#Prints</div>
            <div class="pdo-table-td-itemcolor">Item Color/s</div>
            <div class="pdo-table-td-descriptions">Item / Description</div>
            <div class="pdo-table-td-art">Art</div>
        </div>
        <?php foreach ($orders as $order) { ?>
            <div class="pdo-table-tr">
                <div class="pdo-table-td-move">
                    <?php if ($brand=='SR') { ?>
                        <img class="icon-move" src="/img/printscheduler/move-yellow.svg">
                    <?php } else { ?>
                        <img class="icon-move" src="/img/printscheduler/move-blue.svg">
                    <?php } ?>
                </div>
                <div class="pdo-table-td-icons">
                    <span class="ic-skull">
<!--                        <img class="img-skull" src="/img/printscheduler/icon-skull.svg">-->
                    </span>
                    <span class="ic-rush">
                        <?php if ($order['order_rush']==1) { ?>
                            <img class="img-rush" src="/img/printscheduler/icon-rush.svg">
                        <?php } else { ?>
                            &nbsp;
                        <?php } ?>
                    </span>
                </div>
                <div class="pdo-table-td-ship overdue-ship"><?=date('m/d', $order['shipdate'])?></div>
                <div class="pdo-table-td-order"><?=$order['order_num']?></div>
                <div class="pdo-table-td-items"><?=$order['order_qty']?></div>
                <div class="pdo-table-td-imp"><?=$order['imprint']?></div>
                <div class="pdo-table-td-prints"><?=$order['imprint_qty']?></div>
                <div class="pdo-table-td-itemcolor"><?=$order['item_color']?></div>
                <div class="pdo-table-td-descriptions"><?=$order['item_name']?></div>
                <div class="pdo-table-td-art">
                    <div class="ic-green-art" data-order="<?=$order['order_id']?>">
                        <img class="img-magnifier-white" src="/img/printscheduler/magnifier-white.svg">
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
<?php } ?>
