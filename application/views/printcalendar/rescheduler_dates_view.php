<div class="reschedular-table">
    <div class="reschdltabl-body" id="reschdltabl-body">
        <?php if ($lates > 0) : ?>
        <div class="late-section">
            <div class="latesection-title">LATE ORDERS:</div>
            <div class="reschdltabl-tr reschdltabl-header lateordershead">
                <div class="reschdltabl-td reschdltabl-daylate">Days</div>
                <div class="reschdltabl-td reschdltabl-prcful">%Ful</div>
                <div class="reschdltabl-td reschdltabl-prcship">%Ship</div>
                <div class="reschdltabl-td reschdltabl-approval">Approval</div>
                <div class="reschdltabl-td reschdltabl-brand">&nbsp;</div>
                <div class="reschdltabl-td reschdltabl-rush">&nbsp;</div>
                <div class="reschdltabl-td reschdltabl-order">Order#</div>
                <div class="reschdltabl-td reschdltabl-items">#Items</div>
                <div class="reschdltabl-td reschdltabl-imp">Imp</div>
                <div class="reschdltabl-td reschdltabl-prints">#Prints</div>
                <div class="reschdltabl-td reschdltabl-itmcolor">Item Color/s</div>
                <div class="reschdltabl-td reschdltabl-description">Item / Description</div>
                <div class="reschdltabl-td reschdltabl-inkcolor">Ink Color/s</div>
            </div>
            <div class="dayschedulearea">
                <?php foreach ($lateorders as $list) : ?>
                    <div class="reschdltabl-tr" id="shedulord_<?=$list['order_item_id']?>" draggable="true" ondragstart="dragstartHandler(event)">
                        <div class="reschdltabl-daylatedata"><?=$list['diffdays']?> d</div>
                        <div class="reschdltabl-apprblock lateordershead">
                            <div class="reschdltabl-td reschdltabl-prcful <?=$list['class']=='critical' ? 'peach' : ''?>"><?=$list['fulfillprc']?>%</div>
                            <div class="reschdltabl-td reschdltabl-prcship <?=$list['class']=='critical' ? 'peach' : ''?>"><?=$list['shippedprc']?>%</div>
                            <div class="reschdltabl-td reschdltabl-approval <?=$list['approv']==0 ? 'notapprv' : ''?>"><?=$list['approv']==0 ? 'Not Approved' : 'Approved'?>
                            <?php if ($list['approv'] > 0) : ?>
                                <span class="iconart"><i class="fa fa-search" aria-hidden="true"></i></span>
                            <?php endif; ?>
                            </div>
                        </div>
                        <div class="reschdltabl-mainblock">
                            <div class="reschdltabl-td reschdltabl-brand">
                                <div class="icon-move <?=$list['brand']=='SR' ? 'relievers' : 'stressball'?>">&nbsp;</div>
                            </div>
                            <div class="reschdltabl-td reschdltabl-rush <?=$list['order_rush']==0 ? '' : 'redrush'?>"><?=$list['order_rush']==0 ? '&nbsp;' : 'RUSH'?></div>
                            <div class="reschdltabl-td reschdltabl-order" data-order="<?=$list['order_id']?>"><?=$list['order_num']?></div>
                            <div class="reschdltabl-td reschdltabl-items"><?=QTYOutput($list['item_qty'])?></div>
                            <div class="reschdltabl-td reschdltabl-imp"><?=empty($list['cntprint']) ? '-' : $list['cntprint']?></div>
                            <div class="reschdltabl-td reschdltabl-prints"><?=QTYOutput($list['prints'])?></div>
                            <div class="reschdltabl-td reschdltabl-itmcolor"><?=$list['color']?></div>
                            <div class="reschdltabl-td reschdltabl-description"><?=$list['item']?></div>
                            <div class="reschdltabl-td reschdltabl-inkcolor">&nbsp;</div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        <?php if ($ontime > 0) : ?>
            <div class="ontime-section">
                <div class="ontimesection-title">ON TIME ORDERS:</div>
                <div class="reschdltabl-tr reschdltabl-header">
                    <div class="reschdltabl-td reschdltabl-prcful">%Ful</div>
                    <div class="reschdltabl-td reschdltabl-prcship">%Ship</div>
                    <div class="reschdltabl-td reschdltabl-approval">Approval</div>
                    <div class="reschdltabl-td reschdltabl-brand">&nbsp;</div>
                    <div class="reschdltabl-td reschdltabl-rush">&nbsp;</div>
                    <div class="reschdltabl-td reschdltabl-order">Order#</div>
                    <div class="reschdltabl-td reschdltabl-items">#Items</div>
                    <div class="reschdltabl-td reschdltabl-imp">Imp</div>
                    <div class="reschdltabl-td reschdltabl-prints">#Prints</div>
                    <div class="reschdltabl-td reschdltabl-itmcolor">Item Color/s</div>
                    <div class="reschdltabl-td reschdltabl-description ontimedescription">Item / Description</div>
                    <div class="reschdltabl-td reschdltabl-inkcolor">Ink Color/s</div>
                </div>
                <?php foreach ($calendars as $calendar) : ?>
                    <div class="ontimesection-date" id="printday_<?=$calendar['print_date']?>" ondrop="dropHandler(event)" ondragover="dragoverHandler(event)"><?=date('D - M, j, Y', $calendar['print_date']);?></div>
                    <div class="dayschedulearea" data-printdata="<?=$calendar['print_date']?>">
                        <?php foreach ($calendar['data'] as $list) : ?>
                            <div class="reschdltabl-tr" id="shedulord_<?=$list['order_item_id']?>" draggable="true" ondragstart="dragstartHandler(event)">
                                <div class="reschdltabl-apprblock">
                                    <div class="reschdltabl-td reschdltabl-prcful <?=$list['class']=='critical' ? 'peach' : ''?>"><?=$list['fulfillprc']?>%</div>
                                    <div class="reschdltabl-td reschdltabl-prcship <?=$list['class']=='critical' ? 'peach' : ''?>"><?=$list['shippedprc']?>%</div>
                                    <div class="reschdltabl-td reschdltabl-approval <?=$list['approv']==0 ? 'notapprv' : ''?>"><?=$list['approv']==0 ? 'Not Approved' : 'Approved'?>
                                        <?php if ($list['approv'] > 0) : ?>
                                            <span class="iconart"><i class="fa fa-search" aria-hidden="true"></i></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="reschdltabl-mainblock">
                                    <div class="reschdltabl-td reschdltabl-brand">
                                        <div class="icon-move <?=$list['brand']=='SR' ? 'relievers' : 'stressball'?>">&nbsp;</div>
                                    </div>
                                    <div class="reschdltabl-td reschdltabl-rush <?=$list['order_rush']==0 ? '' : 'redrush'?>"><?=$list['order_rush']==0 ? '&nbsp;' : 'RUSH'?></div>
                                    <div class="reschdltabl-td reschdltabl-order" data-order="<?=$list['order_id']?>"><?=$list['order_num']?></div>
                                    <div class="reschdltabl-td reschdltabl-items"><?=QTYOutput($list['item_qty'])?></div>
                                    <div class="reschdltabl-td reschdltabl-imp"><?=$list['cntprint']?></div>
                                    <div class="reschdltabl-td reschdltabl-prints"><?=QTYOutput($list['prints'])?></div>
                                    <div class="reschdltabl-td reschdltabl-itmcolor"><?=$list['color']?></div>
                                    <div class="reschdltabl-td reschdltabl-description ontimedescription"><?=$list['item']?></div>
                                    <div class="reschdltabl-td reschdltabl-inkcolor">&nbsp;</div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
