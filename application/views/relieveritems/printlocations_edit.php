<?php if (count($locations)==0) { ?>
    <div class="locationsdatarow">&nbsp;</div>
<?php } else { ?>
    <?php $numpp=1;?>
    <?php foreach ($locations as $location) { ?>
        <div class="locationsdatarow">
            <div class="locationdeleterow" data-idx="<?=$location['item_inprint_id']?>"><i class="fa fa-trash"></i></div>
            <div class="locationname">
                <input class="printlocationinpt locationname" data-idx="<?=$location['item_inprint_id']?>" data-item="item_inprint_location" value="<?=$location['item_inprint_location']?>"/>
            </div>
            <div class="locationplace">
                <input class="printlocationinpt locationsize" data-idx="<?=$location['item_inprint_id']?>" data-item="item_inprint_size" value="<?=$location['item_inprint_size']?>"/>
            </div>
            <div class="locationview">
                <?php if (!empty($location['item_inprint_view'])) { ?>
                    <div class="printimageview" data-link="<?=$location['item_inprint_view']?>">
                        <i class="fa fa-search"></i>
                    </div>
                    <div class="printimagedel" data-link="<?=$location['item_inprint_view']?>">
                        <i class="fa fa-search"></i>
                    </div>
                <?php } else {?>
                    <div class="printimageadd" data-idx="<?=$location['item_inprint_id']?>" id="uploadprnloc<?=$location['item_inprint_id']?>"></div>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
<?php } ?>
