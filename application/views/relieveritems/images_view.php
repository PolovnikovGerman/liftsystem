<div class="relievers_itemimages">
    <div class="sectionlabel">IMAGES & OPTIONS:</div>
    <div class="sectionbody">
        <div class="content-row">
            <div class="itemmainimagearea">
                <div class="itemmainimagetitle">Main Image:</div>
                <div class="itemmainimagesrc"><?=$item['main_image']?></div>
            </div>
            <div class="itemcategoryimagearea">
                <div class="itemcategoryimagetitle">Category Page:</div>
                <div class="itemcategoryimagesrc"><?=$item['category_image']?></div>
            </div>
            <div class="itemimagepreview"><i class="fa fa-search"></i></div>
        </div>
        <div class="content-row">
            <div class="itemotherimages">Add’l General Images:</div>
            <div class="itemotherimagesarea"><?=$otherimages?></div>
        </div>
        <div class="content-row">
            <div class="specbannertitle">Special Top Banner:</div>
            <div class="specbannersrc"><?=empty($item['top_banner']) ? 'no image' : $item['top_banner']?></div>
        </div>
        <div class="content-row">
            <div class="itemimages_separator">&nbsp;</div>
        </div>
        <div class="content-row">
            <div class="itemoptionstitle">OPTIONS:</div>
            <div class="itemoptionsvalue">Colors</div>
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
