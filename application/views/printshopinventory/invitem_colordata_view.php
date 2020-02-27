<?php if (isset($uplsess)) { ?>
    <input type="hidden" id="uploadsession" value="<?=$uplsess?>"/>
<?php } ?>
<div class="numpp">&nbsp;</div>
<div class="itemnum">
    <div class="inventorymanage save">
        <i class="fa fa-check-circle"></i>
    </div>
    <div class="inventorymanage cancel">
        <i class="fa fa-times-circle"></i>
    </div>
</div>
<div class="donotreorderedit <?=$notreorder==1 ? 'filled' : ''?>">Do Not Reorder</div>
<div class="coloritemname">
    <input class="invitemcolordata colorname" data-item="color" value="<?= $color ?>"/>
</div>
<?php if ($showmax==1) { ?>
    <div class="itempercent"><?=$percent?></div>        
    <div class="maxinvent" style="display: block">
        <input class="invitemcolordata suggstock fullrow" data-item="suggeststock" value="<?=$suggeststock?>"/>
    </div>
<?php } else { ?>
    <div class="itempercent">
        <input class="invitemcolordata suggstock" data-item="suggeststock" value="<?=$suggeststock?>"/>
    </div>    
<?php } ?>
<div class="instock"><?=$instock?></div>
<div class="reserved"><?= $reserved ?></div>
<div class="available"><?=$availabled?></div>
