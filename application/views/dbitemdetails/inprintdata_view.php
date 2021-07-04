<?php $numpp=1;?>
<?php foreach ($inprints as $inprint) { ?>
    <div class="inprintdatarow">
        <?php if ($editmode==1) { ?>
            <div class="delimprint" data-idx="<?=$inprint['item_inprint_id']?>"><i class="fa fa-trash-o" aria-hidden="true"></i></div>
            <div class="inprintdatanameedit" data-idx="<?=$inprint['item_inprint_id']?>">
                <?=$inprint['item_inprint_location']?>
            </div>
        <?php } else { ?>
            <div class="inprintdataname">
                <?=$numpp?>. <?=$inprint['item_inprint_location']?>
            </div>
        <?php } ?>
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
