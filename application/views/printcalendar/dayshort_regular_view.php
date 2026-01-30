<div class="regular-table">
    <div class="regltabl-tr regltabl-header">
        <div class="regltabl-apprblock">
            <div class="regltabl-td regltabl-prcful">%Ful</div>
            <div class="regltabl-td regltabl-prcship">%Ship</div>
            <div class="regltabl-td regltabl-approval">Approval</div>
        </div>
        <div class="regltabl-mainblock shortresched">
            <div class="regltabl-td regltabl-brand">&nbsp;</div>
            <div class="regltabl-td regltabl-rush">&nbsp;</div>
            <div class="regltabl-td regltabl-order">Order#</div>
            <div class="regltabl-td regltabl-items">#Items</div>
            <div class="regltabl-td regltabl-imp">Imp</div>
            <div class="regltabl-td regltabl-prints">#Prints</div>
            <div class="regltabl-td regltabl-itmcolor shortresched">Item Color/s</div>
            <div class="regltabl-td regltabl-description shortresched">Item / Description</div>
            <div class="regltabl-td regltabl-inkcolor shortresched">Ink Color/s</div>
        </div>
    </div>
    <div id="printshortregularviewarea">
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
                            <span class="iconart"><i class="fa fa-search" aria-hidden="true"></i></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="regltabl-mainblock shortresched <?=$neworderview==0 ? 'repeatrow' : 0?>">
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
                    <div class="regltabl-td regltabl-itmcolor shortresched"><?=$list['color']?></div>
                    <div class="regltabl-td regltabl-description shortresched"><?=$list['item']?></div>
                    <div class="regltabl-td regltabl-inkcolor shortresched">&nbsp;</div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>