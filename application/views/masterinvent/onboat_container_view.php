<div class="onboacontainerarea" data-container="<?= $onboat_container ?>">
    <div class="onboacontainerdata <?= ($onboat_status == 1 ? 'arrived' : '') ?>" data-container="<?= $onboat_container ?>">
        <?php $numpp=0; ?>
        <?php foreach ($data as $item) {?>
            <?php if ($item['inventory_color_id']==0) {?>
                <div class="inventorydatarow masteritem">
                    <div class="conteinerqty">
                        <?=($item['onroutestock']==0 ? '&nbsp;' : QTYOutput($item['onroutestock'])) ?>
                    </div>
                </div>
            <?php } else { ?>
                <div class="inventorydatarow itemcolor <?=$numpp%2==0 ? 'whitedatarow' : 'greydatarow'?>">
                    <div class="conteinerqty">
                        <?=($item['onroutestock']==0 ? '&nbsp;' : QTYOutput($item['onroutestock'])) ?>
                    </div>
                </div>
                <?php $numpp++;?>
            <?php } ?>
        <?php } ?>
    </div>
</div>