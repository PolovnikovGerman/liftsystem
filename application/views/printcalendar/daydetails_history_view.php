<div class="datarow">
    <div class="history-title">History of orders printed or shipped on this day</div>
</div>
<div class="historyblock">
    <div class="history-table">
        <div class="histrtabl-tr histrtabl-header">
            <div class="histrtabl-apprblock">
                <div class="histrtabl-td histrtabl-edit">&nbsp;</div>
                <div class="histrtabl-td histrtabl-approval">Approval</div>
            </div>
            <div class="histrtabl-td histrtabl-printer">Printer</div>
            <div class="histrtabl-mainblock">
                <div class="histrtabl-td histrtabl-brand">&nbsp;</div>
                <div class="histrtabl-td histrtabl-rush">&nbsp;</div>
                <div class="histrtabl-td histrtabl-order">Order#</div>
                <div class="histrtabl-td histrtabl-items">#Items</div>
                <div class="histrtabl-td histrtabl-imp">Imp</div>
                <div class="histrtabl-td histrtabl-prints">#Prints</div>
                <div class="histrtabl-td histrtabl-itmcolor">Item Color/s</div>
                <div class="histrtabl-td histrtabl-description">Item / Description</div>
                <div class="histrtabl-td histrtabl-inkcolor">Ink Color/s</div>
            </div>
            <div class="histrtabl-fulfblock">
                <div class="histrtabl-td histrtabl-printed">Printed</div>
                <div class="histrtabl-td histrtabl-flfkept">Kept</div>
                <div class="histrtabl-td histrtabl-flfmisprt">Misprt</div>
                <div class="histrtabl-td histrtabl-flfproc">%</div>
                <div class="histrtabl-td histrtabl-flftotal">Total</div>
                <div class="histrtabl-td histrtabl-flfplates">Plates</div>
            </div>
            <div class="histrtabl-shipblock">
                <div class="histrtabl-td histrtabl-shipped">Shipped</div>
                <div class="histrtabl-td histrtabl-method">Method</div>
                <div class="histrtabl-td histrtabl-tracking">Tracking#s</div>
            </div>
        </div>
        <?php $order_id = 0; ?>
        <?php foreach ($lists as $list) :?>
            <div class="histrtabl-tr" data-itemcolor="<?=$list['order_itemcolor_id']?>">
                <div class="histrtabl-td histrtabl-edit">
                    <i class="fa fa-pencil" aria-hidden="true"></i>
                </div>
                <div class="histrtabl-apprblock">
                    <div class="histrtabl-td histrtabl-approval <?=$list['approv']==0 ? '' : ''?>">
                        <?=$list['approv']==0 ? 'Not Approved' : 'Approved'?>
                        <?php if ($list['approv'] > 0) :?>
                            <span class="iconart"><i class="fa fa-search" aria-hidden="true"></i></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="histrtabl-td histrtabl-printer"><?=empty($list['user_name']) ? 'Unknown' : $list['user_name']?></div>
                <div class="histrtabl-mainblock">
                    <div class="histrtabl-td histrtabl-brand">
                        <div class="icon-move <?=$list['brand']=='SR' ? 'relievers' : 'stressball'?>">&nbsp;</div>
                    </div>
                    <div class="histrtabl-td histrtabl-rush <?=$list['order_rush']==0 ? '' : 'redrush'?>"><?=$list['order_rush']==0 ? '&nbsp;' : 'RUSH'?></div>
                    <div class="histrtabl-td histrtabl-order">
                        <?php if ($list['order_id']==$order_id) : ?>
                            --
                        <?php else : ?>
                            <?=$list['order_num']?>
                            <?php $order_id=$list['order_id'];?>
                        <?php endif; ?>
                    </div>
                    <div class="histrtabl-td histrtabl-items"><?=empty($list['item_qty']) ? '-' : QTYOutput($list['item_qty'])?></div>
                    <div class="histrtabl-td histrtabl-imp"><?=empty($list['cntprint']) ? '-' : $list['cntprint']?></div>
                    <div class="histrtabl-td histrtabl-prints"><?=empty($list['prints']) ? '-' : QTYOutput($list['prints'])?></div>
                    <div class="histrtabl-td histrtabl-itmcolor"><?=$list['color']?></div>
                    <div class="histrtabl-td histrtabl-description"><?=$list['item']?></div>
                    <div class="histrtabl-td histrtabl-inkcolor">&nbsp;</div>
                </div>
                <div class="histrtabl-fulfblock">
                    <div class="histrtabl-td histrtabl-printed"><?=empty($list['printed']) ? '-' : QTYOutput($list['printed'])?></div>
                    <div class="histrtabl-td histrtabl-flfkept"><?=empty($list['kepted']) ? '-' : QTYOutput($list['kepted'])?></div>
                    <div class="histrtabl-td histrtabl-flfmisprt"><?=empty($list['misprint']) ? '-' : QTYOutput($list['misprint'])?></div>
                    <div class="histrtabl-td histrtabl-flfproc"><?=round($list['misprintprc'],1)?>%</div>
                    <div class="histrtabl-td histrtabl-flftotal"><?=empty($list['amount_sum']) ? '-' : round($list['amount_sum'],0)?></div>
                    <div class="histrtabl-td histrtabl-flfplates"><?=empty($list['plates']) ? '-' : QTYOutput($list['plates'])?></div>
                </div>
                <div class="histrtabl-shipblock">
                    <div class="histrtabl-td histrtabl-shipped"><?=empty($list['shipped']) ? '&nbsp;' : QTYOutput($list['shipped'])?></div>
                    <div class="histrtabl-td histrtabl-method"><?=empty($list['trackservice']) ? '&nbsp;' : $list['trackservice']?></div>
                    <div class="histrtabl-td histrtabl-tracking">
                        <?php if (!empty($list['tracking_id'])) : ?>
                        <input name="trackcode" type="text" readonly="readonly" data-track="<?=$list['tracking_id']?>" value="<?=$list['trackcode']?>"/>
                        <div class="trackbtn" data-track="<?=$list['tracking_id']?>"><i class="fa fa-files-o" aria-hidden="true"></i></div>
                        <?php else : ?>
                        &nbsp;
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="history-totalbox">
        <div class="totalbox-title">Printers Summary:</div>
        <div class="totalbox-table">
            <div class="totalboxtab-tr totalboxtab-header">
                <div class="totalboxtab-td totalboxtab-printers">&nbsp;</div>
                <div class="totalboxtab-td totalboxtab-prints">prints</div>
                <div class="totalboxtab-td totalboxtab-items">items</div>
                <div class="totalboxtab-td totalboxtab-orders">orders</div>
            </div>
            <?php foreach ($totals as $total) : ?>
                <div class="totalboxtab-tr <?=$total['class']=='total' ? 'totalboxtab-footer' : ''?>">
                    <div class="totalboxtab-td totalboxtab-printers"><?=empty($total['user_name']) ? 'Unknown' : $total['user_name']?></div>
                    <div class="totalboxtab-td totalboxtab-prints"><?=empty($total['printqty']) ? '-' : QTYOutput($total['printqty'])?></div>
                    <div class="totalboxtab-td totalboxtab-items"><?=empty($total['itemscnt']) ? '-' : QTYOutput($total['itemscnt'])?></div>
                    <div class="totalboxtab-td totalboxtab-orders"><?=empty($total['ordercnt']) ? '-' : QTYOutput($total['ordercnt'])?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
