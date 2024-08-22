<div class="ready-ship-header">
    <h4>READY TO SHIP:</h4>
    <div class="ready-ship-summary">
        <span><?=QTYOutput($totals['items'])?></span> items,
        <span><?=$totals['orders']?></span> orders</div>
    <div class="rs-btn-link">[view shipped]</div>
</div>
<div class="ready-ship-box">
    <div class="rsbox-table">
        <div class="rsbox-table-tr rsbox-table-header">
            <div class="rsbox-table-td-move">&nbsp;</div>
            <div class="rsbox-table-td-order">Order#</div>
            <div class="rsbox-table-td-itemcolor">Color/s</div>
            <div class="rsbox-table-td-descriptions">Item /Description</div>
            <div class="rsbox-table-td-items">#Items</div>
            <div class="rsbox-table-td-shipqty">Sh Date Qty</div>
            <div class="rsbox-table-td-method">Method</div>
            <div class="rsbox-table-td-tracking">Enter Tracking #s</div>
            <div class="rsbox-table-td-btnsave">&nbsp;</div>
        </div>
    </div>
    <?php $ordernum=''; $displaymain = 0;?>
    <?php foreach($orders as $order) :?>
        <?php if ($order['order_num'] != $ordernum): ?>
            <?php $ordernum = $order['order_num']; $displaymain = 1; ?>
        <?php endif;?>
        <div class="rsbox-table-tr <?=$displaymain==1 ? '' : 'addition'?>">
            <?php if ($displaymain==1) : ?>
                <div class="rsbox-table-td-move">
                    <?php if($brand=='SR'): ?>
                    <img class="icon-move" src="/img/printscheduler/move-yellow.svg">
                    <?php else: ?>
                    <img class="icon-move" src="/img/printscheduler/move-blue.svg">
                    <?php endif; ?>
                </div>
                <div class="rsbox-table-td-order"><?=$order['order_num']?></div>
            <?php endif ?>
            <div class="rsbox-table-td-itemcolor"><?=$order['item_color']?></div>
            <div class="rsbox-table-td-descriptions"><?=$order['item_name']?></div>
            <div class="rsbox-table-td-items"><?=$order['item_qty']?></div>
            <div class="rsbox-table-td-shipqty">
                <div class="date-shipqty <?=$order['shipclass']?>"><?=date('m/d', $order['shipdate'])?></div>
                <input class="inp-shipqty" type="text" data-order="<?=$order['order_itemcolor_id']?>" value="<?=$order['item_qty']?>">
            </div>
            <div class="rsbox-table-td-method">
                <select class="shippingmethodselect" data-order="<?=$order['order_itemcolor_id']?>">
                    <option value=""></option>
                    <option value="UPS">UPS</option>
                    <option value="FedEx">FedEx</option>
                    <option value="DHL">DHL</option>
                    <option value="USPS">USPS</option>
                    <option value="Van">Van</option>
                    <option value="Pickup">Pickup</option>
                    <option value="Courier">Courier</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="rsbox-table-td-tracking">
                <input class="inp-tracking" type="text" placeholder="Enter Tracking #s" data-order="<?=$order['order_itemcolor_id']?>"/>
            </div>
            <div class="rsbox-table-td-btnsave">
                <div class="btn-greensaveship" data-order="<?=$order['order_itemcolor_id']?>">save</div>
            </div>
        </div>
        <?php $displaymain = 0; ?>
    <?php endforeach;?>
</div>
