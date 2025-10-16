<div class="warning-title">
    <div class="warning-title1">WARNING:</div>
    <div class="warning-title2">For these orders, more items shipped (% Shipped) than were printed (% Fulfilled). This indicates a problem that must be resolved before they can be worked on.</div>
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
            <div class="warntabl-td warntabl-description shortresched">Item / Description</div>
            <div class="warntabl-td warntabl-inkcolor">Ink Color/s</div>
        </div>
    </div>
    <?php $order_id=0;?>
    <?php foreach ($lists as $list)  : ?>
        <div class="warntabl-tr">
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
                <div class="warntabl-td warntabl-order" data-order="<?=$list['order_id']?>">
                    <?php if ($order_id!==$list['order_id']) : ?>
                        <?= $list['order_num'] ?>
                        <?php $order_id = $list['order_id']; ?>
                    <?php else: ?>
                        --
                    <?php endif; ?>
                </div>
                <div class="warntabl-td warntabl-items"><?=QTYOutput($list['item_qty'])?></div>
                <div class="warntabl-td warntabl-imp"><?=empty($list['cntprint']) ? '-' : $list['cntprint']?></div>
                <div class="warntabl-td warntabl-prints"><?=QTYOutput($list['prints'])?></div>
                <div class="warntabl-td warntabl-itmcolor"><?=$list['color']?></div>
                <div class="warntabl-td warntabl-description shortresched"><?=$list['item']?></div>
                <div class="warntabl-td warntabl-inkcolor">&nbsp;</div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
