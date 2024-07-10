<?php foreach ($trackings as $tracking) { ?>
    <div class="trackdatarow <?=$completed==1 ? 'completed' : ''?>">
        <input type="hidden" class="trackcodehidden" data-track="<?=$tracking['tracking_id']?>" value="<?=$tracking['trackcode']?>"/>
        <div class="trackqty"><?=$tracking['qty']?></div>
        <div class="trackdate"><?=date('m/d/y', $tracking['trackdate'])?></div>
        <div class="trackservice"><?=$tracking['trackservice']?></div>
        <div class="trackcode"><?=$tracking['trackcode']?></div>
        <div class="trackcodecopy" data-track="<?=$tracking['tracking_id']?>">
            <i class="fa fa-copy"></i>
        </div>
    </div>
<?php } ?>