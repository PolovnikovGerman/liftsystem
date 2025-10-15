<div class="printerline" data-user="<?=$user_id?>">
    <div class="regltabl-printername"><?=$user?></div>
    <div class="regltabl-printerinfo">
        <div class="regltabl-printerinfo-data">
            <span><?=QTYOutput($total['printqty'])?></span> prints - <span><?=QTYOutput($total['itemscnt'])?></span> items - <span><?=$total['ordercnt']?></span> orders
        </div>
    </div>
</div>
<?php $order_id = 0; ?>
<?php foreach ($lists as $list)  :?>
    <div class="regltabl-tr" data-ordercolor="<?=$list['order_itemcolor_id']?>" id="printord_<?=$list['order_item_id']?>" draggable="true" ondragstart="dragstartHandler(event)">
        <div class="regltabl-apprblock">
            <div class="regltabl-td regltabl-prcful <?=$list['class']=='normal' ? '' : 'peach'?>"><?=$list['fulfillprc']?>%</div>
            <div class="regltabl-td regltabl-prcship <?=$list['class']=='normal' ? '' : 'peach'?>"><?=$list['shippedprc']?>%</div>
            <div class="regltabl-td regltabl-approval <?=$list['approv']==0 ? 'notapprv' : ''?>">
                <?=$list['approv']==0 ? 'Not Approved' : 'Approved'?>
                <?php if ($list['approv'] > 0) : ?>
                    <span class="iconart"><i class="fa fa-search" aria-hidden="true"></i></span>
                <?php endif; ?>
            </div>
        </div>
        <div class="regltabl-td regltabl-userprinter">
            <div class="userprinter" data-order="<?=$list['order_itemcolor_id']?>" data-user="<?=$user_id?>">
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
        </div>
        <div class="regltabl-mainblock">
            <div class="regltabl-td regltabl-brand">
                <div class="icon-move <?=$list['brand']=='SR' ? 'relievers' : 'stressball'?>">&nbsp;</div>
            </div>
            <div class="regltabl-td regltabl-rush <?=$list['order_rush']==0 ? '' : 'redrush'?>"><?=$list['order_rush']==0 ? '&nbsp;' : 'RUSH'?></div>
            <div class="regltabl-td regltabl-order">
                <?php if ($list['order_id']==$order_id) : ?>
                    --
                <?php else : ?>
                    <?=$list['order_num']?>
                    <?php $order_id=$list['order_id'];?>
                <?php endif; ?>
            </div>
            <div class="regltabl-td regltabl-items"><?=QTYOutput($list['item_qty'])?></div>
            <div class="regltabl-td regltabl-imp"><?=empty($list['cntprint']) ? '-' : $list['cntprint']?></div>
            <div class="regltabl-td regltabl-prints"><?=QTYOutput($list['prints'])?></div>
            <div class="regltabl-td regltabl-itmcolor shortresched"><?=$list['color']?></div>
            <div class="regltabl-td regltabl-description shortresched"><?=$list['item']?></div>
            <div class="regltabl-td regltabl-inkcolor shortresched">&nbsp;</div>
        </div>
    </div>
<?php endforeach; ?>
