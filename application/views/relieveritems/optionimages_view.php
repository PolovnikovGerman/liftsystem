<?php $numpp=0;?>
<?php foreach ($colors as $color) { ?>
    <?php if ($numpp%2==0) { ?>
        <div class="content-row">
    <?php } ?>
   <?php if ($item['option_images']==1) { ?>
        <div class="itemoptionimagesrc">
            <img class="img-responsive" src="<?=$color['item_color_image']?>"/>
        </div>
    <?php } ?>
    <div class="itemoptionimagelabel"><?=$color['item_color']?></div>
    <?php $numpp++;?>
    <?php if ($numpp%2==0) { ?>
        </div>
    <?php } ?>
<?php } ?>
<?php if ($numpp%2!=0) { ?>
    </div>
<?php } ?>
