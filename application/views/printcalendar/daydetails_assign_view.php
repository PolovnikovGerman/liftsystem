<div class="printerline" data-user="<?=$user_id?>">
    <div class="regltabl-printername"><?=$user?></div>
    <div class="regltabl-printerinfo">
        <div class="regltabl-printerinfo-data">
            <span><?=QTYOutput($total['printqty'])?></span> prints - <span><?=QTYOutput($total['itemscnt'])?></span> items - <span><?=$total['ordercnt']?></span> orders
        </div>
    </div>
</div>
<?php $order_id = 0; ?>
<?php $neworderview=1; ?>
<?php foreach ($lists as $list)  :?>
    <?php if ($list['order_id']!=$order_id) : ?>
        <?php $order_id=$list['order_id'];?>
        <?php $neworderview=1; ?>
    <?php endif; ?>
    <div class="regltabl-tr"  data-ordercolor="<?=$list['order_itemcolor_id']?>">
        <div class="regltabl-apprblock">
            <div class="regltabl-td regltabl-prcful <?=$list['class']=='normal' ? '' : 'peach'?>"><?=$list['fulfillprc']?>%</div>
            <div class="regltabl-td regltabl-prcship <?=$list['class']=='normal' ? '' : 'peach'?>"><?=$list['shippedprc']?>%</div>
            <div class="regltabl-td regltabl-approval <?=$list['approv']==0 ? 'notapprv' : ''?>">
                <?=$list['approv']==0 ? 'Not Approved' : 'Approved'?>
                <?php if ($list['approv'] > 0 && $list['order_blank']==0) : ?>
                    <span class="iconart" data-order="<?=$list['order_id']?>"><i class="fa fa-search" aria-hidden="true"></i></span>
                <?php endif; ?>
            </div>
        </div>
        <div class="regltabl-td regltabl-userprinter">
            <?php if ($neworderview==1) : ?>
            <div class="userprinter" data-order="<?=$list['order_id']?>" data-user="<?=$user_id?>">
                <img src="/img/printscheduler/user-printer.svg">
            </div>
            <div class="assign-popup" data-order="<?=$list['order_id']?>">
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
            <div class="regltabl-td regltabl-rush <?=$list['shipclass']?>"><?=$list['shiplabel']?></div>
            <div class="regltabl-td regltabl-order" data-order="<?=$list['order_id']?>" data-brand="<?=$list['brand']?>"><?=$list['order_num']?></div>
            <?php $neworderview = 0?>
            <?php endif; ?>
            <div class="regltabl-td regltabl-items"><?=QTYOutput($list['item_qty'])?></div>
            <div class="regltabl-td regltabl-imp"><?=empty($list['cntprint']) ? '-' : $list['cntprint']?></div>
            <div class="regltabl-td regltabl-prints"><?=QTYOutput($list['prints'])?></div>
            <div class="regltabl-td regltabl-itmcolor truncateoverflowtext"><?=$list['color']?></div>
            <div class="regltabl-td regltabl-description truncateoverflowtext"><?=$list['item']?></div>
            <div class="regltabl-td regltabl-inkcolor truncateoverflowtext">&nbsp;</div>
        </div>
        <div class="regltabl-prepblock">
            <div class="regltabl-td regltabl-prepared">
                <div class="regltabl-prepstock <?=$list['print_ready']==0 ? '' : 'grey'?>" data-ordercolor="<?=$list['order_itemcolor_id']?>">Stock</div>
                <?php if ($list['order_blank']==0) : ?>
                <div class="regltabl-prepplate <?=$list['plates_ready']==0 ? '' : 'grey'?>" data-orderitem="<?=$list['order_item_id']?>">
                    <div class="prepplate-docview">
                        <?php if ($list['platedocs']==0) : ?>
                            &nbsp;
                        <?php else : ?>
                            <img src="/img/printscheduler/view-files.svg" alt="View files"/>
                        <?php endif; ?>
                    </div>
                    <div class="prepplate-label">Plate</div>
                </div>
                <div class="regltabl-prepink <?=$list['ink_ready']==0 ? '' : 'grey'?>" data-ordercolor="<?=$list['order_itemcolor_id']?>">Ink</div>
                <?php else : ?>
                    <div class="regltabl-blankorder">Blank Order</div>
                <?php endif; ?>
            </div>
        </div>
        <div class="regltabl-fulfblock <?=$list['fulfillprc']>=100 ? 'closedblock' : ''?>">
            <div class="regltabl-td regltabl-done"><?=QTYOutput($list['fulfill'])?></div>
            <div class="regltabl-td regltabl-flfremain"><?=QTYOutput($list['notfulfill'])?></div>
            <div class="regltabl-td regltabl-flfdate">
                <input type="text" name="printdate" data-ordercolor="<?=$list['order_itemcolor_id']?>" value="<?=date('m/d/Y')?>" autocomplete="new-password"/>
            </div>
            <div class="regltabl-td regltabl-flfprint">
                <input type="text" name="printval" data-ordercolor="<?=$list['order_itemcolor_id']?>" autocomplete="new-password"/>
            </div>
            <?php if ($list['order_blank']==0) : ?>
                <div class="regltabl-td regltabl-flfkept">
                    <input type="text" name="keptval" data-ordercolor="<?=$list['order_itemcolor_id']?>" autocomplete="new-password"/>
                </div>
                <div class="regltabl-td regltabl-flfmisprt">
                    <input type="text" name="misprintval" data-ordercolor="<?=$list['order_itemcolor_id']?>" autocomplete="new-password"/>
                </div>
                <div class="regltabl-td regltabl-flftotal"><?=empty($list['amount_sum']) ? '&nbsp;' : round($list['amount_sum'],0)?></div>
                <div class="regltabl-td regltabl-flfplates">
<!--                    <input type="text" name="platesval" data-ordercolor="--><?php //=$list['order_itemcolor_id']?><!--" autocomplete="new-password">-->
                    <select name="platesval" data-ordercolor="<?=$list['order_itemcolor_id']?>">
                        <?php for ($i=0; $i<=50; $i+=0.5) : ?>
                            <option value="<?=$i?>"><?=$i?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            <?php else : ?>
                <div class="rectabl-blankfullfill">Blank Order</div>
            <?php endif; ?>
            <!-- <div class="regltabl-td regltabl-save"> -->
            <div class="btnsave fulfblock <?=$list['fulfillprc']>=100 ? 'closedblock' : ''?>" data-ordercolor="<?=$list['order_itemcolor_id']?>">Save</div>
            <!-- </div> -->
        </div>
        <div class="regltabl-shipblock <?=$list['shippedprc']>=100 ? 'closedblock' : ''?>">
            <?php if (empty($list['fulfill'])) : ?>
            &nbsp;
            <?php else : ?>
            <div class="regltabl-td regltabl-sent"><?=QTYOutput($list['shipped'])?></div>
            <div class="regltabl-td regltabl-shipremain"><?=QTYOutput($list['notshipp'])?></div>
            <div class="regltabl-td regltabl-qty">
                <input type="text" name="shipqty" data-ordercolor="<?=$list['order_itemcolor_id']?>" autocomplete="new-password"/>
            </div>
            <div class="regltabl-td regltabl-shipdate">
                <input type="text" name="shipdate" data-ordercolor="<?=$list['order_itemcolor_id']?>" autocomplete="new-password"/>
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
                <input type="text" name="shiptrackcode" data-ordercolor="<?=$list['order_itemcolor_id']?>" autocomplete="new-password"/>
            </div>
            <!-- <div class="regltabl-td regltabl-save"> -->
                <div class="btnsave shipblock <?=$list['shippedprc']>=100 ? 'closedblock' : ''?>" data-ordercolor="<?=$list['order_itemcolor_id']?>">Save</div>
            <!-- </div> -->
            <?php endif; ?>
        </div>
    </div>
<?php endforeach; ?>
