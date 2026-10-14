<?php $nrow=0;?>
<?php foreach ($items as $orderitem) : ?>
<?php $itemrow = 0;?>
<?php foreach ($orderitem['items'] as $item) : ?>
    <div class="tblitems_tr <?=($nrow%2==0 ? 'whitedatarow' : 'greydatarow')?>">
        <?php if ($itemrow==0) : ?>
            <div class="tblitems_td tblitems_item">
                <?php if ($orderitem['item_id']>0) : ?>
                    <div class="itemmainimageshow" data-event="click" data-css="weekbrandtotals" data-bgcolor="#FFFFFF" data-bordercolor="#adadad"
                         data-textcolor="#FFFFFF" data-position="right" data-balloon="{ajax} /leadorder/viewitemimage?id=<?=$orderitem['item_id']?>" data-timer="4000" data-delay="1000">
                        <img src="/img/leadorder/icon_glass.png" alt="Item Image"/>
                    </div>
                <?php endif; ?>
                <div class="itemnumber">
                    <?=$item['item_number'] ?>
                </div>
                <?php if (!empty($orderitem['inventory_item_id'])) : ?>
                    <div class="inventorycolorshow" data-item="<?=$orderitem['item_id']?>"><i class="fa fa-info-circle" aria-hidden="true"></i></div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="tblitems_td tblitems_item">&nbsp;</div>
        <?php endif; ?>
        <?php if ($orderitem['item_id']>0) : ?>
            <div class="tblitems_td tblitems_descript"><?=$itemrow==0 ? $item['item_description'] : '&nbsp;'?></div>
            <div class="tblitems_td tblitems_color"><?=$item['item_color']?></div>
        <?php else : ?>
            <div class="tblitems_td tblitems_customdescript truncateoverflowtext"><?=$item['item_description']?></div>
        <?php endif; ?>
        <div class="tblitems_td tblitems_qty"><?=$item['item_qty']?></div>
        <div class="tblitems_td tblitems_each"><?=PriceOutput($item['item_price'])?></div>
        <div class="tblitems_td tblitems_subtotal"><?=MoneyOutput($item['item_subtotal'])?></div>
    </div>
    <?php $itemrow++?>
    <?php $nrow++;?>
<?php endforeach; ?>
    <?php $this->load->view('leadordernew/imprint_data_view', array('imprints'=>$orderitem['imprints'])); ?>
<?php endforeach; ?>
