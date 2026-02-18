<?php $order_id = 0;  ?>
<?php $neworderview = 0; ?>
<?php foreach ($lists as $list) : ?>
    <?php if ($list['order_id']!=$order_id) : ?>
        <?php $order_id=$list['order_id'];?>
        <?php $neworderview=1; ?>
    <?php endif; ?>
    <div class="reschdltabl-tr" id="shedulord_<?=$list['order_item_id']?>" draggable="true" ondragstart="dragstartHandler(event)">
        <?php if ($late==1) : ?>
            <div class="reschdltabl-daylatedata"><?=$list['diffdays']?> d</div>
        <?php endif; ?>
        <div class="reschdltabl-apprblock">
            <div class="reschdltabl-td reschdltabl-prcful <?=$list['class']=='critical' ? 'peach' : ''?>"><?=$list['fulfillprc']?>%</div>
            <div class="reschdltabl-td reschdltabl-prcship <?=$list['class']=='critical' ? 'peach' : ''?>"><?=$list['shippedprc']?>%</div>
            <div class="reschdltabl-td reschdltabl-approval <?=$list['approv']==0 ? 'notapprv' : ''?>"><?=$list['approv']==0 ? 'Not Approved' : 'Approved'?>
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
                <div class="reschdltabl-td reschdltabl-rush <?=$list['shipclass']?>"><?=$list['shiplabel']?></div>
                <div class="reschdltabl-td reschdltabl-order" data-order="<?=$list['order_id']?>"><?=$list['order_num']?></div>
                <?php $neworderview = 0 ?>
            <?php endif; ?>
            <div class="reschdltabl-td reschdltabl-items"><?=QTYOutput($list['item_qty'])?></div>
            <div class="reschdltabl-td reschdltabl-imp"><?=empty($list['cntprint']) ? '-' : $list['cntprint']?></div>
            <div class="reschdltabl-td reschdltabl-prints"><?=QTYOutput($list['prints'])?></div>
            <div class="reschdltabl-td reschdltabl-itmcolor truncateoverflowtext"><?=$list['color']?></div>
            <div class="reschdltabl-td reschdltabl-description truncateoverflowtext <?=$late==1 ? '' : 'ontimedescription'?>"><?=$list['item']?></div>
            <div class="reschdltabl-td reschdltabl-inkcolor truncateoverflowtext">&nbsp;</div>
        </div>
    </div>
<?php endforeach; ?>
