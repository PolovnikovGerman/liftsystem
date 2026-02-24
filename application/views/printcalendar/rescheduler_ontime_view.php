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
    <div class="rightsideviewarea"  id="printday_<?=$calendar['print_date']?>" ondrop="dropHandler(event)" ondragover="dragoverHandler(event)">
        <div class="ontimesection-date"><?=date('D - M, j, Y', $calendar['print_date']);?></div>
        <div class="dayschedulearea" data-printdata="<?=$calendar['print_date']?>">
            <?php $order_id = 0;  ?>
            <?php $neworderview = 0; ?>
            <?php foreach ($calendar['data'] as $list) : ?>
                <?php if ($list['order_id']!=$order_id) : ?>
                    <?php $order_id=$list['order_id'];?>
                    <?php $neworderview=1; ?>
                <?php endif; ?>
                <div class="reschdltabl-tr" id="shedulord_<?=$list['order_item_id']?>" draggable="true" ondragstart="dragstartHandler(event)">
                    <div class="reschdltabl-apprblock">
                        <div class="reschdltabl-td reschdltabl-prcful <?=$list['class']=='critical' ? 'peach' : ''?>"><?=$list['fulfillprc']?>%</div>
                        <div class="reschdltabl-td reschdltabl-prcship <?=$list['class']=='critical' ? 'peach' : ''?>"><?=$list['shippedprc']?>%</div>
                        <div class="reschdltabl-td reschdltabl-approval <?=$list['approv']==0 ? 'notapprv' : '' ?>">
                            <?=$list['approv']==0 ? 'Not Approved' : ($list['order_blank']==1 ? 'Blank' : 'Approved')?>
                            <?php if ($list['approv'] > 0 && $list['order_blank']==0) : ?>
                                <span class="iconart" data-order="<?=$list['order_id']?>"><i class="fa fa-search" aria-hidden="true"></i></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="reschdltabl-mainblock <?=$neworderview==0 ? 'repeatrow' : ''?>">
                        <?php if ($neworderview==1) :?>
                            <div class="reschdltabl-td reschdltabl-brand">
                                <div class="icon-move <?=$list['brand']=='SR' ? 'relievers' : 'stressball'?>">&nbsp;</div>
                            </div>
                            <div class="reschdltabl-td reschdltabl-rush <?=$list['shipclass']?>">
                                <?php if (empty($list['shipdate'])) : ?>
                                    <?=$list['shiplabel']?>
                                <?php else : ?>
                                    <div class="shipclasslabel"><?=$list['shiplabel']?></div>
                                    <div class="shipclassvalue"><?=$list['shipdate']?></div>
                                <?php endif; ?>
                            </div>
                            <div class="reschdltabl-td reschdltabl-order" data-order="<?=$list['order_id']?>"><?=$list['order_num']?></div>
                            <?php $neworderview = 0; ?>
                        <?php endif; ?>
                        <div class="reschdltabl-td reschdltabl-items"><?=QTYOutput($list['item_qty'])?></div>
                        <div class="reschdltabl-td reschdltabl-imp"><?=$list['cntprint']?></div>
                        <div class="reschdltabl-td reschdltabl-prints"><?=QTYOutput($list['prints'])?></div>
                        <div class="reschdltabl-td reschdltabl-itmcolor truncateoverflowtext"><?=$list['color']?></div>
                        <div class="reschdltabl-td reschdltabl-description ontimedescription  truncateoverflowtext"><?=$list['item']?></div>
                        <div class="reschdltabl-td reschdltabl-inkcolor truncateoverflowtext">&nbsp;</div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endforeach; ?>
