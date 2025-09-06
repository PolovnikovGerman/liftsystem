<div class="warning-section">
    <div class="warning-title"><span>WARNING:</span> For these orders, more items shipped (% Shipped) than were printed (% Fulfilled). This indicates a problem that must be resolved before they can be worked on.</div>
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
        <?php foreach ($lists as $list) : ?>
            <div class="warntabl-tr" data-ordercolor="<?=$list['order_itemcolor_id']?>">
                <div class="warntabl-apprblock">
                    <div class="warntabl-td warntabl-prcful pink"><?=$list['fulfillprc']?>%</div>
                    <div class="warntabl-td warntabl-prcship pink"><?=$list['shippedprc']?>%</div>
                    <div class="warntabl-td warntabl-approval <?=$list['approv']==0 ? 'notapprv' : ''?>">
                        <?=$list['approv']==0 ? 'Not Approved' : 'Approved'?>
                        <?php if ($list['approv'] > 0) : ?>
                            <span class="iconart"><i class="fa fa-search" aria-hidden="true"></i></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="warntabl-mainblock">
                    <div class="warntabl-td warntabl-brand">
                        <div class="icon-move <?=$list['brand']=='SR' ? 'relievers' : 'stressball'?>">&nbsp;</div>
                    </div>
                    <div class="warntabl-td warntabl-rush <?=$list['order_rush']==0 ? '' : 'redrush'?>"><?=$list['order_rush']==0 ? '&nbsp;' : 'RUSH'?></div>
                    <div class="warntabl-td warntabl-order" data-order="<?=$list['order_id']?>"><?=$list['order_num']?></div>
                    <div class="warntabl-td warntabl-items"><?=QTYOutput($list['item_qty'])?></div>
                    <div class="warntabl-td warntabl-imp"><?=$list['cntprint']?></div>
                    <div class="warntabl-td warntabl-prints"><?=QTYOutput($list['prints'])?></div>
                    <div class="warntabl-td warntabl-itmcolor"><?=$list['color']?></div>
                    <div class="warntabl-td warntabl-description"><?=$list['item']?></div>
                    <div class="warntabl-td warntabl-inkcolor">&nbsp;</div>
                </div>
                <div class="warntabl-fulfblock">
                    <div class="warntabl-td warntabl-done"><?=$list['fulfill']?></div>
                    <div class="warntabl-td warntabl-flfremain"><?=$list['notfulfill']?></div>
                    <div class="warntabl-td warntabl-flfdate"><?=empty($list['amount_date']) ? '&nbsp;' : date('m/d', $list['amount_date'])?></div>
                    <div class="warntabl-td warntabl-flfprint">
                        <input type="text" name="printval" data-ordercolor="<?=$list['order_itemcolor_id']?>"/>
                    </div>
                    <div class="warntabl-td warntabl-flfkept">
                        <input type="text" name="keptval" data-ordercolor="<?=$list['order_itemcolor_id']?>"/>
                    </div>
                    <div class="warntabl-td warntabl-flfmisprt">
                        <input type="text" name="misprintval" data-ordercolor="<?=$list['order_itemcolor_id']?>"/>
                    </div>
                    <div class="warntabl-td warntabl-flftotal"><?=empty($list['amount_sum']) ? '&nbsp;' : round($list['amount_sum'],0)?></div>
                    <div class="warntabl-td warntabl-flfplates">
                        <input type="text" name="platesval" data-ordercolor="<?=$list['order_itemcolor_id']?>"/>
                    </div>
                    <div class="warntabl-td warntabl-save">
                        <div class="btnsave fulfblock" data-ordercolor="<?=$list['order_itemcolor_id']?>">Save</div>
                    </div>
                </div>
                <div class="warntabl-shipblock">
                    <div class="warntabl-td warntabl-sent"><?=$list['shipped']?></div>
                    <div class="warntabl-td warntabl-shipremain"><?=$list['notshipp']<=0 ? '&nbsp;' : round($list['notshipp'],0)?></div>
                    <div class="warntabl-td warntabl-qty">
                        <input type="text" name="shipqty" data-ordercolor="<?=$list['order_itemcolor_id']?>"/>
                    </div>
                    <div class="warntabl-td warntabl-shipdate">
                        <input type="text" name="shipdate" data-ordercolor="<?=$list['order_itemcolor_id']?>"/>
                    </div>
                    <div class="warntabl-td warntabl-method">
                        <select name="shipmethod" data-ordercolor="<?=$list['order_itemcolor_id']?>">
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
                        <input type="text" name="shiptrackcode" data-ordercolor="<?=$list['order_itemcolor_id']?>"/>
                    </div>
                    <div class="warntabl-td warntabl-save">
                        <div class="btnsave shipblock" data-ordercolor="<?=$list['order_itemcolor_id']?>">Save</div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>