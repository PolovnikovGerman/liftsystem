<?php $nrow=0;?>
<div class="orderitemsarea" id="orderitemsarea" data-orderitem="<?=$order_item_id?>">
    <?php foreach ($items as $orderitem) : ?>
    <?php foreach ($orderitem['items'] as $item) : ?>
        <div class="tblitems_tr <?=($nrow%2==0 ? 'whitedatarow' : 'greydatarow')?>">
            <div class="tblitems_td tblitems_item"><?=$item['item_number']?></div>
            <?php if ($orderitem['item_id']>0) : ?>
                <div class="tblitems_td tblitems_descript"><?=$item['item_description']?></div>
                <div class="tblitems_td tblitems_color"><?=$item['item_color']?></div>
            <?php else : ?>
                <div class="tblitems_td tblitems_customdescript truncateoverflowtext"><?=$item['item_description']?></div>
            <?php endif; ?>
            <div class="tblitems_td tblitems_qty"><?=$item['item_qty']?></div>
            <div class="tblitems_td tblitems_each"><?=PriceOutput($item['item_price'])?></div>
            <div class="tblitems_td tblitems_subtotal"><?=MoneyOutput($item['item_subtotal'])?></div>
        </div>
        <?php $nrow++;?>
    <?php endforeach; ?>
        <?php $this->load->view('leadordernew/imprint_data_view', array('imprints'=>$orderitem['imprints'])); ?>
    <?php endforeach; ?>
</div>
