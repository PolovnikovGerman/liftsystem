<div class="reschditms-table">
    <div class="reschditms-tr reschditms-header">
        <div class="reschditms-td reschditms-prcful">%Ful</div>
        <div class="reschditms-td reschditms-prcship">%Ship</div>
        <div class="reschditms-td reschditms-approval">Approval</div>
        <div class="reschditms-td reschditms-brand">&nbsp;</div>
        <div class="reschditms-td reschditms-rush">&nbsp;</div>
<!--        <div class="reschditms-td reschditms-date">Date</div>-->
        <div class="reschditms-td reschditms-order">Order#</div>
        <div class="reschditms-td reschditms-items">#Items</div>
        <div class="reschditms-td reschditms-itmcolor">Item Color/s</div>
        <div class="reschditms-td reschditms-imp">Imp</div>
        <div class="reschditms-td reschditms-prints">#Prints</div>
        <div class="reschditms-td reschditms-inkcolor">Ink Color/s</div>
    </div>
    <div class="reschditms-body" id="reschditms-body">
        <?php foreach ($calendars as $calendar) : ?>
            <div class="reschditms-itemline">
                <div class="imprintitemname"><?=$calendar['item']?></div>
                <div class="imprintorders"><?=$calendar['orders']?> <span>orders</span></div>
                <div class="imprintitemqty"><?=$calendar['items']?></div>
                <div class="imprintprintsqty"><?=$calendar['prints']?></div>
            </div>
            <?php $lists = $calendar['data']; ?>
            <?php foreach ($lists as $list) : ?>
                <div class="reschditms-tr">
                    <div class="reschditms-apprblock">
                        <div class="reschditms-td reschditms-prcful <?=$list['class']=='normal' ? '' : 'peach'?>"><?=$list['fulfillprc']?>%</div>
                        <div class="reschditms-td reschditms-prcship <?=$list['class']=='normal' ? '' : 'peach'?>"><?=$list['shippedprc']?>%</div>
                        <div class="reschditms-td reschditms-approval <?=$list['approv']==0 ? 'notapprv' : ''?>">
                            <?=$list['approv']==0 ? 'Not Approved' : 'Approved'?>
                            <?php if ($list['approv'] > 0) : ?>
                            <span class="iconart"><i class="fa fa-search" aria-hidden="true"></i></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="reschditms-mainblock">
                        <div class="reschditms-td reschditms-brand">
                            <div class="icon-move <?=$list['brand']=='SR' ? 'relievers' : 'stressball'?>">&nbsp;</div>
                        </div>
                        <div class="reschditms-td reschditms-rush <?=$list['shipclass']=='rush' ? 'redrush' : ($list['shipclass']=='late' ? 'redlate' : '')?>">
                            <?php if ($list['shipclass']=='rush') : ?>
                                <div class="shipclasslabel">RUSH</div>
                                <div class="shipclassvalue"><?=date('m/d/y', $list['order_shipdate'])?></div>
                            <?php elseif ($list['shipclass']=='late') : ?>
                                <div class="shipclasslabel">LATE</div>
                                <div class="shipclassvalue"><?=date('m/d/y', $list['order_shipdate'])?></div>
                            <?php else : ?>
                                <div class="shipclassdate"><?=date('m/d/y', $list['order_shipdate'])?></div>
                            <?php endif; ?>
                        </div>
<!--                        <div class="reschditms-td reschditms-date --><?php //=$list['dateclass']?><!--">--><?php //=date('m/d/Y', $list['print_date'])?><!--</div>-->
                        <div class="reschditms-td reschditms-order" data-order="<?=$list['order_id']?>" data-brand="<?=$list['brand']?>"><?=$list['order_num']?></div>
                        <div class="reschditms-td reschditms-items"><?=QTYOutput($list['item_qty'])?></div>
                        <div class="reschditms-td reschditms-itmcolor truncateoverflowtext"><?=$list['color']?></div>
                        <div class="reschditms-td reschditms-imp"><?=empty($list['cntprint']) ? '-' : $list['cntprint']?></div>
                        <div class="reschditms-td reschditms-prints"><?=QTYOutput($list['prints'])?></div>
                        <div class="reschditms-td reschditms-inkcolor  truncateoverflowtext">&nbsp;</div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
</div>