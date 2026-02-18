<div class="warning-title"><span>WARNING:</span> For these orders, more items shipped (% Shipped) than were printed (%
    Fulfilled). This indicates a problem that must be resolved before they can be worked on.
</div>
<div class="warning-close"><i class="fa fa-times" aria-hidden="true"></i></div>
<div class="warning-table">
    <div class="warntabl-tr warntabl-header">
        <div class="warntabl-apprblock">
            <div class="warntabl-td warntabl-prcful">%Ful</div>
            <div class="warntabl-td warntabl-prcship">%Ship</div>
            <div class="warntabl-td warntabl-approval">Approval</div>
        </div>
        <div class="warntabl-mainblock">
            <div class="warntabl-td warntabl-brand">&nbsp;</div>
            <div class="warntabl-td warntabl-rush">&nbsp;</div>
            <div class="warntabl-td warntabl-order">Order#</div>
            <div class="warntabl-td warntabl-items">#Items</div>
            <div class="warntabl-td warntabl-imp">Imp</div>
            <div class="warntabl-td warntabl-prints">#Prints</div>
            <div class="warntabl-td warntabl-itmcolor">Item Color/s</div>
            <div class="warntabl-td warntabl-description">Item / Description</div>
            <div class="warntabl-td warntabl-inkcolor">Ink Color/s</div>
        </div>
        <div class="warntabl-fulfblock">
            <div class="warntabl-td warntabl-done">Done</div>
            <div class="warntabl-td warntabl-flfremain">Remain</div>
            <div class="warntabl-td warntabl-flfdate">Date</div>
            <div class="warntabl-td warntabl-flfprint">Printed</div>
            <div class="warntabl-td warntabl-flfkept">Kept</div>
            <div class="warntabl-td warntabl-flfmisprt">Misprt</div>
            <div class="warntabl-td warntabl-flftotal">Total</div>
            <div class="warntabl-td warntabl-flfplates">Plates</div>
        </div>
        <div class="warntabl-shipblock">
            <div class="warntabl-td warntabl-sent">Sent</div>
            <div class="warntabl-td warntabl-shipremain">Remain</div>
            <div class="warntabl-td warntabl-qty">Qty</div>
            <div class="warntabl-td warntabl-shipdate">Date</div>
            <div class="warntabl-td warntabl-method">Method</div>
            <div class="warntabl-td warntabl-tracking">Tracking#s</div>
        </div>
    </div>
    <?php $order_id=0;?>
    <?php foreach ($lists as $list) : ?>
        <div class="warntabl-tr" data-ordercolor="<?= $list['order_itemcolor_id'] ?>">
            <div class="warntabl-apprblock">
                <div class="warntabl-td warntabl-prcful pink"><?= $list['fulfillprc'] ?>%</div>
                <div class="warntabl-td warntabl-prcship pink"><?= $list['shippedprc'] ?>%</div>
                <div class="warntabl-td warntabl-approval <?= $list['approv'] == 0 ? 'notapprv' : '' ?>">
                    <?= $list['approv'] == 0 ? 'Not Approved' : 'Approved' ?>
                    <?php if ($list['approv'] > 0 && $list['order_blank'] == 0) : ?>
                        <span class="iconart" data-order="<?=$list['order_id']?>"><i class="fa fa-search" aria-hidden="true"></i></span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="warntabl-mainblock">
                <div class="warntabl-td warntabl-brand">
                    <div class="icon-move <?= $list['brand'] == 'SR' ? 'relievers' : 'stressball' ?>">&nbsp;</div>
                </div>
                <div class="warntabl-td warntabl-rush <?=$list['shipclass']?>"><?=$list['shiplabel']?></div>
                <div class="warntabl-td warntabl-order" data-order="<?= $list['order_id'] ?>" data-brand="<?=$list['brand']?>">
                    <?php if ($order_id!==$list['order_id']) : ?>
                        <?= $list['order_num'] ?>
                        <?php $order_id = $list['order_id']; ?>
                    <?php else: ?>
                    --
                    <?php endif; ?>
                </div>
                <div class="warntabl-td warntabl-items"><?= QTYOutput($list['item_qty']) ?></div>
                <div class="warntabl-td warntabl-imp"><?= empty($list['cntprint']) ? '-' : $list['cntprint'] ?></div>
                <div class="warntabl-td warntabl-prints"><?= QTYOutput($list['prints']) ?></div>
                <div class="warntabl-td warntabl-itmcolor truncateoverflowtext"><?= $list['color'] ?></div>
                <div class="warntabl-td warntabl-description truncateoverflowtext"><?= $list['item'] ?></div>
                <div class="warntabl-td warntabl-inkcolor truncateoverflowtext">&nbsp;</div>
            </div>
            <div class="warntabl-fulfblock">
                <div class="warntabl-td warntabl-done"><?= $list['fulfill'] ?></div>
                <div class="warntabl-td warntabl-flfremain"><?= $list['notfulfill'] ?></div>
                <div class="warntabl-td warntabl-flfdate">
                    <input type="text" name="printdate" data-ordercolor="<?=$list['order_itemcolor_id']?>" value="<?=date('m/d/Y')?>" autocomplete="new-password"/>
                </div>
                <div class="warntabl-td warntabl-flfprint">
                    <input type="text" name="printval" data-ordercolor="<?= $list['order_itemcolor_id'] ?>"
                           autocomplete="new-password"/>
                </div>
                <div class="warntabl-td warntabl-flfkept">
                    <input type="text" name="keptval" data-ordercolor="<?= $list['order_itemcolor_id'] ?>"
                           autocomplete="new-password"/>
                </div>
                <div class="warntabl-td warntabl-flfmisprt">
                    <input type="text" name="misprintval" data-ordercolor="<?= $list['order_itemcolor_id'] ?>"
                           autocomplete="new-password"/>
                </div>
                <div class="warntabl-td warntabl-flftotal"><?= empty($list['amount_sum']) ? '&nbsp;' : round($list['amount_sum'], 0) ?></div>
                <div class="warntabl-td warntabl-flfplates">
<!--                    <input type="text" name="platesval" data-ordercolor="--><?php //= $list['order_itemcolor_id'] ?><!--"-->
<!--                           autocomplete="new-password"/>-->
                    <select name="platesval" data-ordercolor="<?=$list['order_itemcolor_id']?>">
                        <?php for ($i=0; $i<=50; $i+=0.5) : ?>
                            <option value="<?=$i?>"><?=$i?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <!-- <div class="warntabl-td warntabl-save"> -->
                <div class="btnsave fulfblock" data-ordercolor="<?= $list['order_itemcolor_id'] ?>">Save</div>
                <!-- </div> -->
            </div>
            <div class="warntabl-shipblock">
                <div class="warntabl-td warntabl-sent"><?= $list['shipped'] ?></div>
                <div class="warntabl-td warntabl-shipremain"><?= $list['notshipp'] <= 0 ? '&nbsp;' : round($list['notshipp'], 0) ?></div>
                <div class="warntabl-td warntabl-qty">
                    <input type="text" name="shipqty" data-ordercolor="<?= $list['order_itemcolor_id'] ?>"
                           autocomplete="new-password"/>
                </div>
                <div class="warntabl-td warntabl-shipdate">
                    <input type="text" name="shipdate" data-ordercolor="<?= $list['order_itemcolor_id'] ?>"/>
                </div>
                <div class="warntabl-td warntabl-method">
                    <select name="shipmethod" data-ordercolor="<?= $list['order_itemcolor_id'] ?>">
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
                <div class="warntabl-td warntabl-tracking">
                    <input type="text" name="shiptrackcode" data-ordercolor="<?= $list['order_itemcolor_id'] ?>"
                           autocomplete="new-password"/>
                </div>
                <!-- <div class="warntabl-td warntabl-save"> -->
                <div class="btnsave shipblock" data-ordercolor="<?= $list['order_itemcolor_id'] ?>">Save</div>
                <!-- </div> -->
            </div>
        </div>
    <?php endforeach; ?>
</div>
