<?php foreach ($trackings as $tracking) { ?>
    <div class="trackdatarow <?=$completed==1 ? 'completed' : ''?>">
        <input type="hidden" class="trackcodehidden" data-track="<?=$tracking['tracking_id']?>" value="<?=$tracking['trackcode']?>"/>
        <div class="trackqty"><?=$tracking['qty']?></div>
        <div class="trackdate"><?=date('m/d/y', $tracking['trackdate'])?></div>
        <div class="trackservice"><?=$tracking['trackservice']?></div>
        <div class="trackcode">
            <?php $url = trackcodeurl($tracking['trackservice'], $tracking['trackcode']); ?>
            <?php if (!empty($url)) { ?>
                <a class="trackservicelnk" data-lnkdata="<?=$url?>"><?=$tracking['trackcode']?></a>
            <?php } else { ?>
                <?=$tracking['trackcode']?>
            <?php } ?>
        </div>
        <div class="trackcodecopy" data-track="<?=$tracking['tracking_id']?>">
            <i class="fa fa-copy"></i>
        </div>
    </div>
<?php } ?>