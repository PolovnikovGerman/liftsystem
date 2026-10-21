<?php $nrow=0;?>
<?php $showitem = 0; ?>
<?php foreach ($items as $orderitem) : ?>
    <?php $itemrow = 0;?>
    <?php foreach ($orderitem['items'] as $item) : ?>
        <?php if (!empty($orderitem['item_id'])) : ?>
            <div class="tblitems_tr <?=($nrow%2==0 ? 'whitedatarow' : 'greydatarow')?>">
                <?php if ($itemrow==0) : ?>
                    <div class="tblitems_td tblitems_item">
                        <?php if ($orderitem['item_id']>0) : ?>
                            <div class="itemmainimageshow" data-event="hover" data-css="weekbrandtotals" data-bgcolor="#FFFFFF" data-bordercolor="#adadad"
                                 data-textcolor="#FFFFFF" data-position="right" data-balloon="{ajax} /leadorder/viewitemimage?id=<?=$orderitem['item_id']?>" data-timer="4000" data-delay="1000">
                                <img src="/img/leadorder/icon_glass.png" alt="Item Image"/>
                            </div>
                        <?php endif; ?>
                        <div class="tblitems_itemnumber"><?=$item['item_number']?></div>
                        <?php if (!empty($orderitem['inventory_item_id'])) : ?>
                            <div class="inventorycolorshow" data-item="<?=$orderitem['order_item_id']?>"><i class="fa fa-info-circle" aria-hidden="true"></i></div>
                        <?php endif; ?>
                    </div>
                <?php else : ?>
                    <div class="tblitems_td tblitems_item">&nbsp;</div>
                <?php endif; ?>
                <?php if ($orderitem['item_id']>0) : ?>
                    <div class="tblitems_td tblitems_descript">
                        <?php if ($itemrow==0) : ?>
                        <input type="text" class="orderitemdata" data-orderitem="<?=$orderitem['order_item_id']?>" data-item="<?=$item['item_id']?>"
                               data-fld="item_description" value="<?=$item['item_description']?>"/>
                        <?php else: ?>
                        &nbsp;
                        <?php endif; ?>
                    </div>
                    <div class="tblitems_td tblitems_color truncateoverflowtext">
                        <?=$item['out_colors']?>
                    </div>
                <?php else : ?>
                    <div class="tblitems_td tblitems_customdescript">
                        <input type="text" class="orderitemdata" data-orderitem="<?=$orderitem['order_item_id']?>" data-item="<?=$item['item_id']?>"
                        data-fld="item_description" value="<?=$item['item_description']?>"/>
                    </div>
                <?php endif; ?>
                <div class="tblitems_td tblitems_qty">
                    <input type="text" class="orderitemdata" data-orderitem="<?=$orderitem['order_item_id']?>" data-item="<?=$item['item_id']?>"
                    data-fld="item_qty" value="<?=$item['item_qty']?>"/>
                </div>
                <div class="tblitems_td tblitems_each">
                    <input type="text" class="orderitemdata" data-orderitem="<?=$orderitem['order_item_id']?>" data-item="<?=$item['item_id']?>"
                           data-fld="item_price" value="<?=$item['item_price']?>"/>
                </div>
                <div class="tblitems_td tblitems_subtotal" data-orderitem="<?=$orderitem['order_item_id']?>" data-item="<?=$item['item_id']?>">
                    <?=MoneyOutput($item['item_subtotal'])?>
                </div>
<!--                --><?php //if ($item['item_row']==1) : ?>
                    <div class="tblitems_td tditems_trash" data-orderitem="<?=$orderitem['order_item_id']?>" data-item="<?=$item['item_id']?>" data-itemname="<?=$item['item_description']?>">
                        [&mdash;]
                    </div>
<!--                --><?php //endif; ?>
            </div>
            <?php $nrow++;?>
        <?php endif; ?>
        <?php $itemrow++;?>
    <?php endforeach; ?>
    <?php if ($orderitem['num_colors'] > 1) : ?>
        <div class="tblitems_tr <?=($nrow%2==0 ? 'whitedatarow' : 'greydatarow')?>" style="text-align: center">
            <span class="addorderitemcolor textgreen"  data-orderitem="<?=$orderitem['order_item_id']?>">+color</span>
        </div>
    <?php endif; ?>
    <?php if (count($orderitem['imprints']) > 0) : ?>
    <div class="itemimprintdetailsarea" data-orderitem="<?=$orderitem['order_item_id']?>">
        <?php $this->load->view('leadordernew/imprint_data_view', ['order_item_id' =>$orderitem['order_item_id'], 'imprints'=>$orderitem['imprints']]); ?>
    </div>
    <?php endif; ?>
<?php endforeach; ?>
<div class="tblitems_tr lastitemrow" data-orderitem="<?=$orderitem['order_item_id']?>">
<?php if (empty($orderitem['item_id'])) : ?>
    <?php $this->load->view('leadordernew/itemadd_data_view', array('items' => $orderitem['items'], 'itemslist' => $itemslist)); ?>
<?php else : ?>
    <div class="tblitems_td textgreen addnewitem" data-orderitem="<?=$orderitem['order_item_id']?>'?>">+Add Item</div>
<?php endif; ?>
</div>
