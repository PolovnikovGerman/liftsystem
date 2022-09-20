<input type="hidden" id="optionsimages" value="<?=$item['option_images']?>"/>
<div class="content-row">
    <div class="imagetitle">Options:</div>
    <div class="itemoptionsvalue">
        <select class="itemdetailsoptions <?=empty($item['options']) ? 'missing_info' : ''?>">
            <option value=""></option>
            <option value="Colors" <?=$item['options']=='colors' ? 'selected="selected"' : ''?>>Colors</option>
            <option value="Flavors" <?=$item['options']=='flavors' ? 'selected="selected"' : ''?>>Flavors</option>
            <option value="Sizes" <?=$item['options']=='sizes' ? 'selected="selected"' : ''?>>Sizes</option>
            <option value="Shapes" <?=$item['options']=='shapes' ? 'selected="selected"' : ''?>>Shapes</option>
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
    <div id="addoptiontxt" style="<?=$item['option_images']==0 ? '' : 'display: none;'?>">
        <i class="fa fa-plus"></i> Add Option
    </div>
    <div id="addoptionimage" style="<?=$item['option_images']==1 ? '' : 'display: none;'?>"></div>
</div>
<div class="colorimages-slider">
    <?=$slider?>
</div>
