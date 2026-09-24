<?php $nrow=0;?>
<?php $showitem = 0; ?>
<div class="orderitemsarea" id="orderitemsarea">
    <?php foreach ($items as $orderitem) : ?>
        <?php foreach ($orderitem['items'] as $item) : ?>
            <?php if (!empty($orderitem['item_id'])) : ?>
            <div class="tblitems_tr <?=($nrow%2==0 ? 'whitedatarow' : 'greydatarow')?>">
                <div class="tblitems_td tblitems_item"><?=$item['item_number']?></div>
                <?php if ($orderitem['item_id']>0) : ?>
                    <div class="tblitems_td tblitems_descript">
                        <input type="text" class="orderitemdata" data-orderitem="<?=$orderitem['order_item_id']?>" data-item="<?=$item['item_id']?>"
                               data-fld="item_description" value="<?=$item['item_description']?>"/>
                    </div>
                    <div class="tblitems_td tblitems_color">
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
                <div class="tblitems_td tditems_trash">
                    <?php if ($item['item_row']==1) : ?>
                        <i class="fa fa-trash" data-orderitem="<?=$orderitem['order_item_id']?>" data-item="<?=$item['item_description']?>"></i>
                    <?php  else : ?>
                        &nbsp;
                    <?php endif; ?>
                </div>
            </div>
            <?php $nrow++;?>
            <?php endif; ?>
        <?php endforeach; ?>
        <?php if ($orderitem['num_colors'] > 1) : ?>
            <?php $nrow++ ?>
            <div class="tblitems_tr <?=($nrow%2==0 ? 'whitedatarow' : 'greydatarow')?>" style="text-align: center">
                <span class="addorderitemcolor textgreen"  data-orderitem="<?=$orderitem['order_item_id']?>">+color</span>
            </div>
        <?php endif; ?>
        <?php if (count($orderitem['imprints']) > 0) : ?>
            <?php $this->load->view('leadordernew/imprint_data_view', ['order_item_id' =>$orderitem['order_item_id'], 'imprints'=>$orderitem['imprints']]); ?>
        <?php endif; ?>
    <?php endforeach; ?>
    <div class="tblitems_tr" data-orderitem="<?=$orderitem['order_item_id']?>">
    <?php if (empty($orderitem['item_id'])) : ?>
        <?php $this->load->view('leadordernew/itemadd_data_view', array('items' => $orderitem['items'], 'itemslist' => $itemslist)); ?>
    <?php else : ?>
        <div class="tblitems_td textgreen addnewitem" data-orderitem="<?=$orderitem['order_item_id']?>'?>">+Add Item</div>
    <?php endif; ?>
    </div>
</div>

