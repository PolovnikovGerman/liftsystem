<?php foreach ($trackings as $tracking) { ?>
        <?php if (intval($tracking['qty'])>0) { ?>
        <div class="trackdatarow <?=$completed==1 ? 'completed' : ''?>">
            <input type="hidden" class="trackcodehidden" data-track="<?=$tracking['tracking_id']?>" value="<?=$tracking['trackcode']?>"/>
            <div class="trackqty"><?=$tracking['qty']?></div>
            <div class="trackdate"><?=date('m/d/y', $tracking['trackdate'])?></div>
            <div class="trackservice"><?=$tracking['trackservice']?></div>
            <?php $url = trackcodeurl($tracking['trackservice'], $tracking['trackcode']); ?>
            <?php if (!empty($url)) : ?>
                <div class="trackcode trackservicelnk" data-lnkdata="<?=$url?>">
                    <input type="text" class="trackcodeinpt" data-track="<?=$tracking['tracking_id']?>" value="<?=$tracking['trackcode']?>" readonly="readonly"/>
                </div>
            <?php  else : ?>
                <div class="trackcode" data-lnkdata="<?=$url?>">
                    <?=$tracking['trackcode']?>
            <?php endif; ?>
            <div class="trackcodecopy <?=empty($tracking['trackcode']) ? 'emptycopy' : '' ?>" data-track="<?=$tracking['tracking_id']?>">
                <i class="fa fa-copy"></i>
            </div>
        </div>
        <?php } ?>
<?php } ?>