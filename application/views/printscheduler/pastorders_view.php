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
        <?php $ordernum = ''; $displaymain = 0; ?>
        <?php foreach ($orders as $order) : ?>
            <?php if ($order['order_num']!==$ordernum) : ?>
                <?php $ordernum = $order['order_num']; $displaymain = 1; ?>
            <?php endif; ?>
            <div class="pdo-table-tr <?=$displaymain==1 ? '' : 'addition'?>">
                <?php if ($displaymain==1) : ?>
                    <div class="pdo-table-td-move" data-order="<?=$order['order_itemcolor_id']?>">
                        <?php if ($brand=='SR') : ?>
                            <img class="icon-move" src="/img/printscheduler/move-yellow.svg">
                        <?php  else : ?>
                            <img class="icon-move" src="/img/printscheduler/move-blue.svg">
                        <?php endif; ?>
                    </div>
                    <div class="pdo-table-td-icons">
                        <div class="ic-skull <?=$order['stock_class']?>">
                            <img class="img-skull" src="/img/printscheduler/icon-skull.svg">
                        </div>
                        <div class="ic-rush">
                            <?php if ($order['order_rush']==1) : ?>
                                <img class="img-rush" src="/img/printscheduler/icon-rush.svg">
                            <?php else : ?>
                                &nbsp;
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="pdo-table-td-ship overdue-ship"><?=date('m/d', $order['shipdate'])?></div>
                    <div class="pdo-table-td-order"><?=$order['order_num']?></div>
                <?php endif; ?>
                <div class="pdo-table-td-items"><?=$order['item_qty']?></div>
                <div class="pdo-table-td-imp"><?=$order['imprints']?></div>
                <div class="pdo-table-td-prints"><?=$order['prints']?></div>
                <div class="pdo-table-td-itemcolor"><?=$order['item_color']?></div>
                <div class="pdo-table-td-descriptions"><?=$order['item_name']?></div>
                <div class="pdo-table-td-art">
                    <div class="ic-green-art" data-order="<?=$order['order_itemcolor_id']?>">
                        <img class="img-magnifier-white" src="/img/printscheduler/magnifier-white.svg">
                    </div>
                </div>
            </div>
            <?php $displaymain = 0;?>
        <?php endforeach; ?>
    </div>
<?php } ?>
