<div class="content-row">
    <div class="imagetitle">Options:</div>
    <div class="itemoptionsvalue">
        <select class="itemdetailsoptions <?=empty($item['options']) ? 'missing_info' : ''?>" disabled="disabled">
            <option value=""></option>
            <option value="Colors" <?=$item['options']=='Colors' ? 'selected="selected"' : ''?>>Colors</option>
            <option value="Flavors" <?=$item['options']=='Flavors' ? 'selected="selected"' : ''?>>Flavors</option>
            <option value="Sizes" <?=$item['options']=='Sizes' ? 'selected="selected"' : ''?>>Sizes</option>
            <option value="Shapes" <?=$item['options']=='Shapes' ? 'selected="selected"' : ''?>>Shapes</option>
        </select>
    </div>
    <div class="itemoptioncheck">
        <?php if ($item['option_images']==1) { ?>
            <i class="fa fa-check-square"></i>
        <?php } else { ?>
            <i class="fa fa-square-o"></i>
        <?php } ?>
    </div>
    <div class="itemoptionchecklabel">Require Images</div>
</div>
<div class="colorimages-slider">
    <?=$slider?>
</div>
