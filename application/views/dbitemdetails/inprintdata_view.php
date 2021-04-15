<?php $numpp=1;?>
<?php foreach ($inprints as $inprint) { ?>
    <div class="inprintdatarow">
        <div class="inprintdataname"><?=$numpp?>. <?=$inprint['item_inprint_location']?></div>
        <div class="inprintdatasize"><?=$inprint['item_inprint_size']?></div>
        <div class="inprintdataview" data-viewurl="<?=$inprint['item_inprint_view']?>">click</div>
    </div>
    <?php $numpp++;?>
<?php } ?>
<?php if ($numpp<9) { ?>
    <?php for ($i=$numpp; $i<9; $i++) { ?>
        <div class="inprintdatarow">
            <div class="inprintdataname">&nbsp;</div>
            <div class="inprintdatasize">&nbsp;</div>
            <div class="inprintdataview">&nbsp;</div>
        </div>
    <?php } ?>
<?php } ?>
