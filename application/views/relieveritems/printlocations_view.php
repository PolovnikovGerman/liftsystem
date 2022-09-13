<?php if (count($locations)==0) { ?>
    <div class="locationsdatarow">&nbsp;</div>
<?php } else { ?>
    <?php $numpp=1;?>
    <?php foreach ($locations as $location) { ?>
        <div class="locationsdatarow">
            <div class="locationname"><?=$numpp?>. <?=htmlspecialchars_decode($location['item_inprint_location'])?></div>
            <div class="locationplace"><?=htmlspecialchars_decode($location['item_inprint_size'])?></div>
            <div class="locationview">
                <?php if (!empty($location['item_inprint_view'])) { ?>
                    <div data-link="<?=$location['item_inprint_view']?>">
                        <i class="fa fa-search"></i>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
<?php } ?>
