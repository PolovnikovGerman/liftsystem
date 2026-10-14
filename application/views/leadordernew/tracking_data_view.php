<?php foreach ($trackings as $tracking): ?>
    <?php $viewrow = 1; ?>
    <?php if (intval($tracking['qty'])==0 && $edit==0) $viewrow = 0; ?>
    <?php if ($viewrow==1) : ?>
        <div class="fulflmshipping_box">
            <input class="fulflmship_inptqty trackdatainpt" data-item="<?=$order_item?>" data-color="<?=$item_color?>" data-track="<?=$tracking['tracking_id']?>" data-fld="qty" type="text" name="" value="<?=$tracking['qty']?>" <?=$edit==0 ? 'readonly="readonly"' : ''?>/>
            <input class="fulflmship_inptdate trackdatainpt"  data-item="<?=$order_item?>" data-color="<?=$item_color?>" data-track="<?=$tracking['tracking_id']?>" data-fld="trackdate" type="text" name="" placeholder="09/17/2026" value="<?=date('m/d/Y', $tracking['trackdate'])?>" <?=$edit==0 ? 'readonly="readonly"' : ''?>/>
            <select class="fulflmship_inptcarrier" data-item="<?=$order_item?>" data-color="<?=$item_color?>" data-track="<?=$tracking['tracking_id']?>" <?=$edit==0 ? 'disabled="disabled"' : ''?>>
                <option value=""></option>
                <?php foreach ($services as $service): ?>
                <option value="<?=$service['key']?>" <?=$service['key']==$tracking['trackservice'] ? 'selected="selected"' : ''?>><?=$service['value']?></option>?>
                <?php endforeach; ?>
            </select>
            <?php $url = trackcodeurl($tracking['trackservice'], $tracking['trackcode']); ?>
            <input type="text" class="fulflmship_inpttrack  trackdatainpt" data-item="<?=$order_item?>" data-color="<?=$item_color?>" data-track="<?=$tracking['tracking_id']?>" data-fld="trackcode" value="<?=$tracking['trackcode']?>" <?=$edit==0 ? 'readonly="readonly"' : ''?>/>
            <div class="copytrackcode" data-item="<?=$order_item?>" data-color="<?=$item_color?>" data-track="<?=$tracking['tracking_id']?>"><i class="fa fa-clone" aria-hidden="true"></i></div>
        </div>
    <?php endif; ?>
<?php endforeach; ?>