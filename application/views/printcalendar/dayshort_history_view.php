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
                <div class="histrtabl-td histrtabl-description shortresched">Item / Description</div>
                <div class="histrtabl-td histrtabl-inkcolor">Ink Color/s</div>
            </div>
        </div>
        <?php foreach ($lists as $list) :?>
            <div class="histrtabl-tr">
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
                <div class="histrtabl-td histrtabl-printer"><?=empty($list['user_name']) ? 'Unsign' : $list['user_name']?></div>
                <div class="histrtabl-mainblock">
                    <div class="histrtabl-td histrtabl-brand">
                        <div class="icon-move <?=$list['brand']=='SR' ? 'relievers' : 'stressball'?>">&nbsp;</div>
                    </div>
                    <div class="histrtabl-td histrtabl-rush <?=$list['order_rush']==0 ? '' : 'redrush'?>"><?=$list['order_rush']==0 ? '&nbsp;' : 'RUSH'?></div>
                    <div class="histrtabl-td histrtabl-order"><?=$list['order_num']?></div>
                    <div class="histrtabl-td histrtabl-items"><?=empty($list['item_qty']) ? '-' : QTYOutput($list['item_qty'])?></div>
                    <div class="histrtabl-td histrtabl-imp"><?=empty($list['cntprint']) ? '-' : $list['cntprint']?></div>
                    <div class="histrtabl-td histrtabl-prints"><?=empty($list['prints']) ? '-' : QTYOutput($list['prints'])?></div>
                    <div class="histrtabl-td histrtabl-itmcolor"><?=$list['color']?></div>
                    <div class="histrtabl-td histrtabl-description shortresched"><?=$list['item']?></div>
                    <div class="histrtabl-td histrtabl-inkcolor">&nbsp;</div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>