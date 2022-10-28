<div class="custom_galleries_area">
    <?php $numpp = 0;?>
    <?php foreach ($galleries as $gallery) { ?>
        <?php if ($numpp==0) { ?>
            <div class="gallerydatarow" style="clear: both; width: 100%">
        <?php } ?>
        <div class="gallerydataserction" style="width: 33%;">
            <div class="content-row">
                <div class="label custom_gallerytitle">Example Type <?=$gallery['numpp']?>:</div>
                <input class="custom_gallerytitle" readonly="readonly" name="gallery_name" data-gallery="<?=$gallery['custom_gallery_id']?>" value="<?=$gallery['gallery_name']?>"/>
            </div>
            <div class="content-row">
                <div class="custom_exampletitlesize">(366px x 244px)</div>
            </div>
            <div class="content-row">
                <div class="custom_examplelabel">
                    <div class="custom_exampleimagetitle">Type Image <?=$gallery['numpp']?>:</div>
                    <div class="customimage_enlarge"> click to enlarge </div>
                </div>
                <div class="gallery_collageimage" <?=empty($gallery['gallery_image']) ? '' : 'data-fancybox="gallery_example" data-src="'.$gallery['gallery_image'].'"'?>>
                    <?php if (!empty($gallery['gallery_image'])) { ?>
                        <img src="<?=$gallery['gallery_image']?>" alt="Gallery Image"/>
                    <?php } else { ?>
                        &nbsp;
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php $numpp++;?>
        <?php if ($numpp==3) { ?>
            </div>
            <?php $numpp=0;?>
        <?php } ?>
    <?php } ?>
    <?php if ($numpp>0) { ?>
        </div>
    <?php } ?>
</div>
