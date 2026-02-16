<div class="printerline">
    <div class="regltabl-printername unassignedtotal">Unassigned</div>
    <div class="regltabl-printerinfo unassignedtotal">
        <div class="regltabl-printerinfo-data">
            <span><?=QTYOutput($total['printqty'])?></span> prints - <span><?=QTYOutput($total['itemscnt'])?></span> items - <span><?=$total['ordercnt']?></span> orders
        </div>
    </div>
</div>
<?php $order_id = 0; ?>
<?php $neworderview=1; ?>
<?php foreach ($lists as $list) : ?>
    <?php if ($list['order_id']!=$order_id) : ?>
        <?php $order_id=$list['order_id'];?>
        <?php $neworderview=1; ?>
    <?php endif; ?>
    <div class="regltabl-tr" data-ordercolor="<?=$list['order_itemcolor_id']?>" id="printord_<?=$list['order_item_id']?>" draggable="true" ondragstart="dragstartHandler(event)">
        <div class="regltabl-apprblock">
            <div class="regltabl-td regltabl-prcful <?=$list['class']=='normal' ? '' : 'peach'?>"><?=$list['fulfillprc']?>%</div>
            <div class="regltabl-td regltabl-prcship <?=$list['class']=='normal' ? '' : 'peach'?>"><?=$list['shippedprc']?>%</div>
            <div class="regltabl-td regltabl-approval <?=$list['approv']==0 ? 'notapprv' : ''?>">
                <?=$list['approv']==0 ? 'Not Approved' : 'Approved'?>
                <?php if ($list['approv'] > 0) : ?>
                    <span class="iconart" data-order="<?=$list['order_id']?>"><i class="fa fa-search" aria-hidden="true"></i></span>
                <?php endif; ?>
            </div>
        </div>
        <div class="regltabl-td regltabl-userprinter">
            <?php if ($neworderview == 1) : ?>
            <div class="userprinter" data-order="<?=$list['order_itemcolor_id']?>" data-user="0">
                <img src="/img/printscheduler/user-printer.svg">
            </div>
            <div class="assign-popup" data-order="<?=$list['order_itemcolor_id']?>">
                <ul>
                    <li class="assignusr" data-user="0">Unassigned</li>
                    <?php foreach ($users as $user) : ?>
                        <li class="assignusr" data-user="<?=$user['user_id']?>"><?=$user['first_name']?></li>
                    <?php endforeach;?>
                </ul>
            </div>
            <?php else: ?>
            &nbsp;
            <?php endif; ?>
        </div>
        <div class="regltabl-mainblock <?=$neworderview==0 ? 'repeatrow' : ''?>">
            <?php if ($neworderview==1) :?>
                <div class="regltabl-td regltabl-brand">
                    <div class="icon-move <?=$list['brand']=='SR' ? 'relievers' : 'stressball'?>">&nbsp;</div>
                </div>
                <div class="regltabl-td regltabl-rush <?=$list['order_rush']==0 ? '' : 'redrush'?>"><?=$list['order_rush']==0 ? '&nbsp;' : 'RUSH'?></div>
                <div class="regltabl-td regltabl-order" data-order="<?=$list['order_id']?>"><?=$list['order_num']?></div>
                <?php $neworderview = 0?>
            <?php endif; ?>
            <div class="regltabl-td regltabl-items"><?=QTYOutput($list['item_qty'])?></div>
            <div class="regltabl-td regltabl-imp"><?=empty($list['cntprint']) ? '-' : $list['cntprint']?></div>
            <div class="regltabl-td regltabl-prints"><?=QTYOutput($list['prints'])?></div>
            <div class="regltabl-td regltabl-itmcolor shortresched truncateoverflowtext"><?=$list['color']?></div>
            <div class="regltabl-td regltabl-description shortresched truncateoverflowtext"><?=$list['item']?></div>
            <div class="regltabl-td regltabl-inkcolor shortresched truncateoverflowtext">&nbsp;</div>
        </div>
    </div>
<?php endforeach; ?>
