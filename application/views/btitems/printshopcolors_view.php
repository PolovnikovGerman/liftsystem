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
    <div class="itemoptionimagelabel editmode">
        <select class="printshopcolor" data-color="<?=$color['item_color_id']?>">
            <option value="" <?=$color['item_color']=='' ? 'selected="selected"' : ''?>></option>
            <option value="<?=$color['item_color_source']?>" <?=$color['item_color_source']==$color['item_color'] ? 'selected="selected"' : ''?>><?=$color['item_color_source']?></option>
        </select>
    </div>
    <?php $numpp++;?>
    <?php if ($numpp%2==0) { ?>
        </div>
    <?php } ?>
<?php } ?>
<?php if ($numpp%2!=0) { ?>
    </div>
<?php } ?>