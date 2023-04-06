<?php $numpp=0; ?>
<input type="hidden" id="container_session" value="<?=$session_id?>"/>
<?php foreach ($data as $item) {?>
    <?php if ($item['inventory_color_id']==0) {?>
        <div class="inventorydatarow masteritem">
            <div class="conteinerqty" data-itemtotal="<?=$item['inventory_item_id']?>">
                <?=($item['onroutestock']==0 ? '&nbsp;' : QTYOutput($item['onroutestock'])) ?>
            </div>
        </div>
    <?php } else { ?>
        <div class="inventorydatarow itemcolor <?=$numpp%2==0 ? 'whitedatarow' : 'greydatarow'?>">
            <div class="conteinerqty">
                <input type="text" class="onroutestockinpt" value="<?=$item['onroutestock']?>" data-color="<?=$item['inventory_color_id']?>" data-item="<?=$item['inventory_item_id']?>"/>
            </div>
            <div class="conteinerprice">
                <input type="text" class="onroutepriceinpt" value="<?=$item['vendor_price']?>" data-color="<?=$item['inventory_color_id']?>" data-item="<?=$item['inventory_item_id']?>"/>
            </div>
        </div>
        <?php $numpp++;?>
    <?php } ?>
<?php } ?>
