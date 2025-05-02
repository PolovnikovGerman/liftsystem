<div class="shipped-header">
    <h4>SHIPPED ORDER:</h4>
    <div class="shipped-summary">
        <span><?=QTYOutput($totals['items'])?></span> items,
        <span><?=$totals['orders']?></span> orders
    </div>
    <div class="shipped-btn-link">[hide]</div>
</div>
<div class="ready-ship-box">
    <div class="rsbox-table">
        <div class="rsbox-table-tr rsbox-table-header">
            <div class="rsbox-table-td-move">&nbsp;</div>
            <div class="rsbox-table-td-approval">Approval</div>
            <div class="rsbox-table-td-order">Order#</div>
            <div class="rsbox-table-td-itemcolor">Color/s</div>
            <div class="rsbox-table-td-descriptions">Item /Description</div>
            <div class="rsbox-table-td-items">#Items</div>
            <div class="rsbox-table-td-shipdate" style="width: 42px;">Sh Date</div>
            <div class="rsbox-table-td-qty" style="width: 48px">Qty</div>
            <div class="rsbox-table-td-method">Method</div>
            <div class="rsbox-table-td-tracking">Enter Tracking #s</div>
            <div class="rsbox-table-td-btnsave">&nbsp;</div>
        </div>
            <?php $ordernum = ''; $displaymain = 0;?>
            <?php foreach ($orders as $order) :?>
            <?php if ($order['order_num']!==$ordernum):?>
                <?php $ordernum = $order['order_num']; $displaymain = 1;?>
            <?php endif; ?>
            <div class="rsbox-table-tr shiporderdatarow <?=$displaymain==1 ? '' : 'addition'?>">
                <?php if ($displaymain==1) : ?>
                    <div class="rsbox-table-td-move">
                        <?php if ($order['brand']=='SR'): ?>
                        <img class="icon-move" src="/img/printscheduler/move-yellow.svg">
                        <?php else: ?>
                        <img class="icon-move" src="/img/printscheduler/move-blue.svg">
                        <?php endif; ?>
                    </div>
                    <div class="rsbox-table-td-approval <?=$order['order_blank']==1 ? '' : ($order['approved']==0 ? 'notapproved' : '')?>">
                        <?=$order['order_blank']==1 ? 'Blank' : ($order['approved']==0 ? 'Not Approved' : 'Approved')?>
                    </div>
                    <div class="rsbox-table-td-order" data-order="<?=$order['order_id']?>"><?=$order['order_num']?></div>
                <?php endif; ?>
                <div class="rsbox-table-td-itemcolor"><?=$order['item_color']?></div>
                <div class="rsbox-table-td-descriptions"><?=$order['item_name']?></div>
                <div class="rsbox-table-td-items"><?=$order['item_qty']?></div>
                <div class="rsbox-table-td-shipdate <?=$order['shipclass']?>"><?=date('m/d', $order['shipdate'])?></div>
                <div class="rsbox-table-td-qty">
                    <input class="inp-shipqty" type="text" readonly="readonly" placeholder="1200" value="<?=$order['shipqty']?>"/>
                </div>
                <div class="rsbox-table-td-method">
                    <select disabled="disabled">
                        <option value=""></option>
                        <option value="UPS" <?=$order['shipmethod']=='UPS' ? 'selected="selected"' : ''?>>UPS</option>
                        <option value="FedEx" <?=$order['shipmethod']=='FedEx' ? 'selected="selected"' : ''?>>FedEx</option>
                        <option value="DHL" <?=$order['shipmethod']=='DHL' ? 'selected="selected"' : ''?>>DHL</option>
                        <option value="USPS" <?=$order['shipmethod']=='USPS' ? 'selected="selected"' : ''?>>USPS</option>
                        <option value="Van" <?=$order['shipmethod']=='Van' ? 'selected="selected"' : ''?>>Van</option>
                        <option value="Pickup" <?=$order['shipmethod']=='Pickup' ? 'selected="selected"' : ''?>>Pickup</option>
                        <option value="Courier" <?=$order['shipmethod']=='Courier' ? 'selected="selected"' : ''?>>Courier</option>
                        <option value="Other" <?=$order['shipmethod']=='Other' ? 'selected="selected"' : ''?>>Other</option>
                    </select>
                </div>
                <div class="rsbox-table-td-tracking">
                    <input class="inp-tracking" type="text" readonly="readonly" placeholder="20 2458 4578 459 46" value="<?=$order['trackcode']?>">
                </div>
                <div class="rsbox-table-td-btnsave">
                    <div class="icon-check"><img class="img-check" src="/img/printscheduler/check-green.svg"></div>
                </div>
            </div>
            <?php $displaymain = 0;?>
        <?php endforeach;?>
    </div>
</div>

