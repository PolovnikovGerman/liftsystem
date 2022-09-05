<?php if (count($locations)==0) { ?>
    <div class="locationsdatarow">&nbsp;</div>
<?php } else { ?>
    <?php $numpp=1;?>
    <?php foreach ($locations as $location) { ?>
        <div class="locationsdatarow">
            <div class="locationname"><?=$numpp?>. <?=$location['item_inprint_location']?></div>
            <div class="locationplace"><?=$location['item_inprint_size']?></div>
            <div class="locationview">
                <?php if (!empty($location['item_inprint_view'])) { ?>
                    <div class="printimageview" data-link="<?=$location['item_inprint_view']?>">
                        <i class="fa fa-search"></i>
                    </div>
                    <div class="printimagedel" data-link="<?=$location['item_inprint_view']?>">
                        <i class="fa fa-search"></i>
                    </div>
                <?php } else {?>
                    <div class="printimageadd" data-link="<?=$location['item_inprint_view']?>">browse</div>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
<?php } ?>
