<div class="brandmultiaccsessarea" style="float: left; width: 244px;">
    <?php foreach ($brands as $brand) { ?>
        <div class="brandname">
            <?= $brand['label'] ?>
            <div class="brandaccesscheckfld" style="float: right; margin: 0 5px; cursor: pointer;" data-menuitem="<?=$menu_item?>" data-brand="<?=$brand['brand']?>">
            <?php if ($brand['checkval'] == 1) { ?>
                <i class="fa fa-check-square-o" aria-hidden="true"></i>
            <?php } else { ?>
                <i class="fa fa-square-o" aria-hidden="true"></i>
            <?php } ?>
            </div>
        </div>
    <?php } ?>
</div>
