<div class="optionstextdata">
    <?php $numpp=0;?>
    <?php foreach ($colors as $color) { ?>
        <?php if ($numpp%7==0) { ?>
            <div class="optionstextarea">
        <?php } ?>
        <div class="content-row">
            <input type="text" class="itemimagecaption optimage" data-image="<?=$color['item_color_id']?>" value="<?=$color['item_color']?>" placeholder="Enter Option"/>
        </div>
        <?php $numpp++;?>
        <?php if ($numpp%7==0) { ?>
            </div>
        <?php } ?>
    <?php } ?>
    <?php if ($numpp%7!==0) { ?>
        </div>
    <?php } ?>
</div>