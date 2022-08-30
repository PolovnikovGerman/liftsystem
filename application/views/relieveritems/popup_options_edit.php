<div class="content-row">
    <div class="imagetitle">Options:</div>
    <div class="itemoptionsvalue">
        <select class="itemdetailsoptions <?=empty($item['options']) ? 'missing_info' : ''?>">
            <option value=""></option>
            <option value="Colors" <?=$item['options']=='Colors' ? 'selected="selected"' : ''?>>Colors</option>
            <option value="Flavors" <?=$item['options']=='Flavors' ? 'selected="selected"' : ''?>>Flavors</option>
            <option value="Sizes" <?=$item['options']=='Sizes' ? 'selected="selected"' : ''?>>Sizes</option>
            <option value="Shapes" <?=$item['options']=='Shapes' ? 'selected="selected"' : ''?>>Shapes</option>
        </select>
    </div>
    <div class="itemoptioncheck editmode">
        <?php if ($item['option_images']==1) { ?>
            <i class="fa fa-check-square"></i>
        <?php } else { ?>
            <i class="fa fa-square-o"></i>
        <?php } ?>
    </div>
    <div class="itemoptionchecklabel">Require Images</div>
    <div id="addoptionimage" style="<?=$item['option_images']==1 ? '' : 'display: none;'?>"></div>
</div>
<div class="colorimages-slider" style="<?=$item['option_images']==1 ? 'visibility: visible;' : 'visibility: hidden;'?>">
    <?=$slider?>
</div>
