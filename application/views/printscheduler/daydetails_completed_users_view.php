<div class="rpbox-printer">
    <div class="rpbox-printer-header">
        <h4><?=$user_name?>:</h4>
        <div class="rpbox-printer-summary">
            <span><?=QTYOutput($totals['prints'])?></span> prints,
            <span><?=QTYOutput($totals['items'])?></span> items,
            <span><?=$totals['orders']?></span> orders
        </div>
    </div>
    <div class="rpbox-printer-table">
        <div class="rpbox-table-tr rpbox-table-header">
            <div class="rpbox-table-td-move">&nbsp;</div>
            <div class="rpbox-table-td-icons">&nbsp;</div>
            <div class="rpbox-table-td-assign">Assign</div>
            <div class="rpbox-table-td-ship">Ship</div>
            <div class="rpbox-table-td-order">Order#</div>
            <div class="rpbox-table-td-print">Print</div>
            <div class="rpbox-table-td-items">#Items</div>
            <div class="rpbox-table-td-imp">Imp</div>
            <div class="rpbox-table-td-prints">#Prints</div>
            <div class="rpbox-table-td-itemcolor">Item Color/s</div>
            <div class="rpbox-table-td-descriptions">Item / Description</div>
            <div class="rpbox-table-td-inputs"><span class="span-good">Good</span><span class="span-kept">Kept</span><span class="span-mispt">Mispt</span><span class="span-plate">Plate</span></div>
        </div>
        <?php $ordernum = ''; $displaymain =0;?>
        <?php foreach ($orders as $order): ?>
            <?php if ($order['order_num']!==$ordernum): ?>
                <?php $ordernum = $order['order_num']; $displaymain = 1;?>
            <?php endif; ?>
            <div class="rpbox-table-tr <?=$displaymain==1 ? '' : 'addition'?>">
                <?php if ($displaymain==1): ?>
                    <div class="rpbox-table-td-move">
                        <?php if ($order['brand']=='SR') : ?>
                            <img class="icon-move" src="/img/printscheduler/move-yellow.svg">
                        <?php else: ?>
                            <img class="icon-move" src="/img/printscheduler/move-blue.svg">
                        <?php endif; ?>
                    </div>
                    <div class="rpbox-table-td-icons">
                        <div class="ic-skull <?=$order['stock_class']?>">
                            <img class="img-skull" src="/img/printscheduler/icon-skull.svg">
                        </div>
                        <div class="ic-rush">
                            <?php if ($order['order_rush']==1) : ?>
                                <img class="img-rush" src="/img/printscheduler/icon-rush.svg">
                            <?php else: ?>
                                &nbsp;
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="rpbox-table-td-assign">
                        <div class="ic-assign">
                            <img class="img-icon-user" src="/img/printscheduler/icon-user-white.svg">
                        </div>
                    </div>
                    <div class="rpbox-table-td-ship <?=$order['shipclass']?>"><?=date('m/d', $order['shipdate'])?></div>
                    <div class="rpbox-table-td-order"><?=$order['order_num']?></div>
                    <div class="rpbox-table-td-print">
                        <div class="ic-purpul-print" data-order="<?=$order['order_id']?>"><img class="img-icon-print" src="/img/printscheduler/icon-print-white.svg"></div>
                    </div>
                <?php endif; ?>
                <div class="rpbox-table-td-items <?=$order['qtyclass']?>"><?=$order['item_qty']?></div>
                <div class="rpbox-table-td-imp"><?=$order['imprints']?></div>
                <div class="rpbox-table-td-prints"><?=$order['prints']?></div>
                <div class="rpbox-table-td-itemcolor"><?=$order['item_color']?></div>
                <div class="rpbox-table-td-descriptions"><?=$order['item_name']?></div>
                <div class="rpbox-table-td-inputs">
                    <input class="rdbox-inp-good" type="text" data-order="<?=$order['order_itemcolor_id']?>" data-color="<?=$order['inventory_color']?>">
                    <input class="rdbox-inp-kept" type="text" data-order="<?=$order['order_itemcolor_id']?>" data-color="<?=$order['inventory_color']?>">
                    <input class="rdbox-inp-mispt" type="text" data-order="<?=$order['order_itemcolor_id']?>" data-color="<?=$order['inventory_color']?>">
                    <input class="rdbox-inp-plate" type="text" data-order="<?=$order['order_itemcolor_id']?>" data-color="<?=$order['inventory_color']?>">
                    <div class="btn-greydone" data-order="<?=$order['order_itemcolor_id']?>" data-color="<?=$order['inventory_color']?>">done</div>
                </div>
            </div>
            <?php $displaymain=0;?>
        <?php endforeach;?>
    </div>
</div>