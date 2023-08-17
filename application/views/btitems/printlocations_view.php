<?php $numpp=1;?>
<?php foreach ($locations as $location) { ?>
    <div class="locationsdatarow">
        <div class="locationnumber"><?=$numpp?>.</div>
        <div class="locationnameview"><?=$location['item_inprint_location']?></div>
        <div class="locationplace"><?=$location['item_inprint_size']?></div>
        <div class="locationview">
            <?php if (!empty($location['item_inprint_view'])) { ?>
                <div class="printlocexample" data-link="<?=$location['item_inprint_view']?>">
                    <i class="fa fa-search"></i>
                </div>
            <?php } ?>
        </div>
    </div>
    <?php $numpp++;?>
<?php } ?>
<?php if ($numpp < 12) { ?>
    <?php for ($i=$numpp; $i<13; $i++) { ?>
        <div class="locationsdatarow">
            <div class="locationname"><?=$i?>. </div>
            <div class="locationplace">&nbsp;</div>
            <div class="locationview">&nbsp;</div>
        </div>
    <?php } ?>
<?php } ?>