<div class="relievers_itemimages">
    <div class="sectionlabel">IMAGES & OPTIONS:</div>
    <div class="sectionbody">
        <div class="content-row">
            <div class="itemmainimagearea">
                <div class="itemmainimagetitle">Main Image:</div>
                <div class="itemmainimagesrc">
                    <?php if (!empty($item['main_image'])) {?>
                        <img src="<?=$item['main_image']?>" alt="Main Image">
                    <?php } ?>
                </div>
            </div>
            <div class="itemcategoryimagearea">
                <div class="itemcategoryimagetitle">Category Page:</div>
                <div class="itemcategoryimagesrc">
                    <?php if (!empty($item['category_image'])) {?>
                    <?php } ?>
                </div>
            </div>
            <div class="itemimagepreview"><i class="fa fa-search"></i></div>
        </div>
        <div class="content-row">
            <div class="itemotherimages">Add’l General Images:</div>
            <div class="itemotherimagesarea"><?=$otherimages?></div>
        </div>
        <div class="content-row">
            <div class="specbannertitle">Special Top Banner:</div>
            <div class="specbannersrc">
                <?php if (empty($item['top_banner'])) { ?>
                    no image
                <?php } else { ?>
                    <img src="<?=$item['top_banner']?>" alt="top banner"/>
                <?php } ?>
            </div>
        </div>
        <div class="content-row">
            <div class="itemimages_separator">&nbsp;</div>
        </div>
        <div class="content-row">
            <div class="itemoptionstitle">OPTIONS:</div>
            <div class="itemoptionsvalue">
                <select class="itemdetailsoptions <?=empty($item['options']) ? 'missing_info' : ''?>" disabled>
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
        <div class="content-row">
            <div class="itemimages_separator">&nbsp;</div>
        </div>
        <div class="itemoptionsarea"><?=$optionsimg?></div>
    </div>
</div>
