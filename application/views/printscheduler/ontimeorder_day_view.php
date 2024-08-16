<div class="day-block-open" data-orderday="<?=$dayhead['printdate']?>">
    <div class="day-block-header">
        <div class="day-name">
            <h4><?=date('D - M j, Y', strtotime($dayhead['printdate']))?></h4>
            <div class="day-name-arrow open" data-orderday="<?=$dayhead['printdate']?>">
                <img class="day-name-arrow-up" src="/img/printscheduler/chevron-up-white.svg">
            </div>
        </div>
        <div class="day-summary">
            <span><?=$dayhead['totalimpr']?></span> prints,
            <span><?=$dayhead['totalitems']?></span> items,
            <span><?=$dayhead['cntorder']?></span> orders
        </div>
        <div class="day-arrow-open" data-orderday="<?=$dayhead['printdate']?>">
            <img class="long-arrow-right" src="/img/printscheduler/long-arrow-right-black.svg">
        </div>
    </div>
    <div class="current-table" data-orderday="<?=$dayhead['printdate']?>">
        <div class="itm-table-tr itm-table-header">
            <div class="itm-table-td-move">&nbsp;</div>
            <div class="itm-table-td-icons">&nbsp;</div>
            <div class="itm-table-td-ship">Ship</div>
            <div class="itm-table-td-order">Order#</div>
            <div class="itm-table-td-items">#Items</div>
            <div class="itm-table-td-imp">Imp</div>
            <div class="itm-table-td-prints">#Prints</div>
            <div class="itm-table-td-itemcolor">Item Color/s</div>
            <div class="itm-table-td-descriptions">Item / Description</div>
            <div class="pdo-table-td-art">Art</div>
        </div>
        <?php $ordernum = ''; $displaymain = 0;?>
        <?php foreach ($orders as $order): ?>
            <?php if ($order['order_num']!==$ordernum): ?>
                <?php $ordernum = $order['order_num']; $displaymain = 1; ?>
            <?php endif; ?>
            <div class="itm-table-tr <?=$displaymain==1 ? '' : 'addition'?>">
                <?php if ($displaymain==1): ?>
                    <div class="itm-table-td-move">
                        <?php if ($brand=='SR') { ?>
                            <img class="icon-move" src="/img/printscheduler/move-yellow.svg">
                        <?php } else { ?>
                            <img class="icon-move" src="/img/printscheduler/move-blue.svg">
                        <?php } ?>
                    </div>
                    <div class="itm-table-td-icons">
                        <div class="ic-skull" style="opacity: 0;"><img class="img-skull" src="/img/printscheduler/icon-skull.svg"></div>
                        <div class="ic-rush">
                        <?php if ($order['order_rush']==1) { ?>
                            <img class="img-rush" src="/img/printscheduler/icon-rush.svg">
                        <?php } else { ?>
                            &nbsp;
                        <?php } ?>
                        </div>
                    </div>
                    <div class="itm-table-td-ship"><?=date('m/d', $order['shipdate'])?></div>
                    <div class="itm-table-td-order"><?=$order['order_num']?></div>
                <?php endif; ?>
                <div class="itm-table-td-items"><?=$order['order_qty']?></div>
                <div class="itm-table-td-imp"><?=$order['imprints']?></div>
                <div class="itm-table-td-prints"><?=$order['prints']?></div>
                <div class="itm-table-td-itemcolor"><?=$order['item_color']?></div>
                <div class="itm-table-td-descriptions"><?=$order['item_name']?></div>
                <div class="itm-table-td-art">
                    <div class="ic-green-art" data-order="<?=$order['order_id']?>">
                        <img class="img-magnifier-white" src="/img/printscheduler/magnifier-white.svg">
                    </div>
                </div>
            </div>
            <?php $displaymain=0;?>
        <?php endforeach; ?>
    </div>
</div>