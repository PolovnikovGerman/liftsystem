<?php foreach ($trackings as $tracking): ?>
    <?php if (intval($tracking['qty'])>0) : ?>
        <div class="fulflmshipping_box">
            <input class="fulflmship_inptqty" type="text" name="" value="<?=$tracking['qty']?>" <?=$edit==0 ? 'readonly="readonly"' : ''?>/>
            <input class="fulflmship_inptdate" type="text" name="" placeholder="09/17/2026" value="<?=date('m/d/y', $tracking['trackdate'])?>" <?=$edit==0 ? 'readonly="readonly"' : ''?>/>
            <select class="fulflmship_inptcarrier"  <?=$edit==0 ? 'disabled="disabled"' : ''?>>
                <option value=""></option>
                <?php foreach ($services as $service): ?>
                <option value="<?=$service['key']?>" <?=$service['key']==$tracking['trackservice'] ? 'selected="selected"' : ''?>><?=$service['value']?></option>?>
                <?php endforeach; ?>
            </select>
            <?php $url = trackcodeurl($tracking['trackservice'], $tracking['trackcode']); ?>
            <input type="text" class="fulflmship_inpttrack" data-track="<?=$tracking['tracking_id']?>" value="<?=$tracking['trackcode']?>" <?=$edit==0 ? 'readonly="readonly"' : ''?>/>
        </div>
    <?php endif; ?>
<?php endforeach; ?>