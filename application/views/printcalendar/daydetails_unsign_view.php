<div class="regltabl-tr printerline">
    <div class="regltabl-printername">Unassigned</div>
    <div class="regltabl-printerinfo"><span><?=QTYOutput($total['printqty'])?></span> prints - <span><?=QTYOutput($total['itemscnt'])?></span> items - <span><?=$total['ordercnt']?></span> orders</div>
</div>
<?php foreach ($lists as $list) : ?>
    <div class="regltabl-tr" data-ordercolor="<?=$list['order_itemcolor_id']?>">
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
            <div class="userprinter" data-order="<?=$list['order_itemcolor_id']?>" data-user="0">
                <img src="/img/printscheduler/user-printer.svg">
            </div>
            <div class="assign-popup" data-order="<?=$list['order_itemcolor_id']?>">
                <ul>
                    <li class="assignusr" data-user="0">Unassign</li>
                    <?php foreach ($users as $user) : ?>
                        <li class="assignusr" data-user="<?=$user['user_id']?>"><?=$user['first_name']?></li>
                    <?php endforeach;?>
                </ul>
            </div>
        </div>
        <div class="regltabl-mainblock">
            <div class="regltabl-td regltabl-brand">
                <div class="icon-move">
                    <?php if ($list['brand']=='SR') : ?>
                        <img src="/img/printscheduler/move-yellow.svg">
                    <?php else: ?>
                        <img src="/img/printscheduler/move-blue.svg">
                    <?php endif; ?>
                </div>
            </div>
            <div class="regltabl-td regltabl-rush <?=$list['order_rush']==0 ? '' : 'redrush'?>"><?=$list['order_rush']==0 ? '&nbsp;' : 'RUSH'?></div>
            <div class="regltabl-td regltabl-order" data-order="<?=$list['order_id']?>"><?=$list['order_num']?></div>
            <div class="regltabl-td regltabl-items"><?=QTYOutput($list['item_qty'])?></div>
            <div class="regltabl-td regltabl-imp"><?=$list['cntprint']?></div>
            <div class="regltabl-td regltabl-prints"><?=QTYOutput($list['prints'])?></div>
            <div class="regltabl-td regltabl-itmcolor"><?=$list['color']?></div>
            <div class="regltabl-td regltabl-description"><?=$list['item']?></div>
            <div class="regltabl-td regltabl-inkcolor">&nbsp;</div>
        </div>
        <div class="regltabl-prepblock">
            <div class="regltabl-td regltabl-prepared">
                <div class="regltabl-prepstock <?=$list['print_ready']==0 ? 'grey' : ''?>" data-ordercolor="<?=$list['order_itemcolor_id']?>">Stock</div>
                <div class="regltabl-prepplate <?=$list['plates_ready']==0 ? 'grey' : ''?>" data-orderitem="<?=$list['order_item_id']?>">Plate</div>
                <div class="regltabl-prepink <?=$list['ink_ready']==0 ? 'grey' : ''?>" data-ordercolor="<?=$list['order_itemcolor_id']?>">Ink</div>
            </div>
        </div>
        <div class="regltabl-fulfblock <?=$list['fulfillprc']>=100 ? 'closedblock' : ''?>">
            <div class="regltabl-td regltabl-done"><?=QTYOutput($list['fulfill'])?></div>
            <div class="regltabl-td regltabl-flfremain"><?=QTYOutput($list['notfulfill'])?></div>
            <div class="regltabl-td regltabl-flfprint">
                <input type="text" name="printval" data-ordercolor="<?=$list['order_itemcolor_id']?>"/>
            </div>
            <div class="regltabl-td regltabl-flfkept">
                <input type="text" name="keptval" data-ordercolor="<?=$list['order_itemcolor_id']?>"/>
            </div>
            <div class="regltabl-td regltabl-flfmisprt">
                <input type="text" name="misprintval" data-ordercolor="<?=$list['order_itemcolor_id']?>"/>
            </div>
            <div class="regltabl-td regltabl-flftotal"><?=empty($list['amount_sum']) ? '&nbsp;' : round($list['amount_sum'],0)?></div>
            <div class="regltabl-td regltabl-flfplates">
                <input type="text" name="platesval" data-ordercolor="<?=$list['order_itemcolor_id']?>">
            </div>
            <div class="regltabl-td regltabl-save">
                <div class="btnsave fulfblock" data-ordercolor="<?=$list['order_itemcolor_id']?>">Save</div>
            </div>
        </div>
        <div class="regltabl-shipblock <?=$list['shippedprc']>=100 ? 'closedblock' : ''?>">
            <div class="regltabl-td regltabl-sent"><?=QTYOutput($list['shipped'])?></div>
            <div class="regltabl-td regltabl-shipremain"><?=QTYOutput($list['notshipp'])?></div>
            <div class="regltabl-td regltabl-qty">
                <input type="text" name="shipqty" data-ordercolor="<?=$list['order_itemcolor_id']?>"/>
            </div>
            <div class="regltabl-td regltabl-shipdate">
                <input type="text" name="shipdate" data-ordercolor="<?=$list['order_itemcolor_id']?>"/>
            </div>
            <div class="regltabl-td regltabl-method">
                <select name="shipmethod" data-ordercolor="<?=$list['order_itemcolor_id']?>">
                    <option value=""></option>
                    <option value="UPS">UPS</option>
                    <option value="FedEx">FedEx</option>
                    <option value="DHL">DHL</option>
                    <option value="USPS">USPS</option>
                    <option value="Van">Van</option>
                    <option value="Pickup">Pickup</option>
                    <option value="Courier">Courier</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="regltabl-td regltabl-tracking">
                <input type="text" name="shiptrackcode" data-ordercolor="<?=$list['order_itemcolor_id']?>"/>
            </div>
            <div class="regltabl-td regltabl-save">
                <div class="btnsave shipblock" data-ordercolor="<?=$list['order_itemcolor_id']?>">Save</div>
            </div>
        </div>
    </div>
<?php endforeach; ?>
