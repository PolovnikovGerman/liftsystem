<input type="hidden" id="onboatsession" value="<?=$session?>"/>
<div class="onboacontainerdata">
    <?php $numpp = 0; ?>
    <?php foreach ($data as $row) { ?>
        <div class="contanerdataval <?= ($row['type'] == 'item' ? 'itemdata totalitem' : ($numpp % 2 == 0 ? 'white' : 'grey')) ?>" data-item="<?=$row['printshop_item_id']?>">
            <?php if ($row['type']=='item') { ?>
            <?=$row['onroutestock']?>
            <?php } else { ?>
            <input class="onboatelementinput" data-color="<?=$row['printshop_color_id']?>" value="<?=($row['numval']==0 ? '' :$row['numval'])?>"/>
            <?php } ?>
        </div>
        <?php if ($row['type'] == 'color') $numpp++; ?>
    <?php } ?>
</div>
