<?php $numpp=0;?>
<?php foreach ($imgoptions as $imgoption) { ?>
    <?php if ($numpp%2==0) { ?>
        <div class="content-row">
    <?php } ?>
    <div class="itemoptionimagesrc">
        <img class="img-responsive" src="<?=$imgoption['item_img_name']?>"/>
    </div>
    <div class="itemoptionimagelabel"><?=$imgoption['item_img_label']?></div>
    <?php $numpp++;?>
    <?php if ($numpp%2==0) { ?>
        </div>
    <?php } ?>
<?php } ?>
<?php if ($numpp%2!=0) { ?>
    </div>
<?php } ?>
