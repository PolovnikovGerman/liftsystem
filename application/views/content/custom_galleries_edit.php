<?php $numpp = 0;?>
<?php foreach ($galleries as $gallery) { ?>
    <?php if ($numpp==0) { ?>
        <div class="gallerydatarow" style="clear: both; width: 100%">
    <?php } ?>
    <div class="gallerydataserction" style="width: 33%;">
        <div class="content-row">
            <div class="label custom_gallerytitle">Example Type <?=$gallery['numpp']?>:</div>
            <input class="custom_gallerytitle" data-content="gallery" data-field="gallery_name" data-gallery="<?=$gallery['custom_gallery_id']?>" value="<?=$gallery['gallery_name']?>"/>
            <div class="custom_gallerydelete" data-gallery="<?=$gallery['custom_gallery_id']?>">
                <i class="fa fa-times-circle" aria-hidden="true" title="Remove Gallery"></i>
            </div>
        </div>
        <div class="content-row">
            <div class="custom_exampletitlesize">(366px x 244px)</div>
        </div>
        <div class="content-row">
            <div class="custom_examplelabel">
                <div class="custom_exampleimagetitle">Type Image <?=$gallery['numpp']?>:</div>
                <div class="customimage_enlarge"> click to enlarge </div>
            </div>
            <div class="gallery_collageimage">
                <?php if (!empty($gallery['gallery_image'])) { ?>
                <img src="<?=$gallery['gallery_image']?>" alt="Gallery Image"/>
                </div>
                <div class="custom_galleryimagedelete" data-gallery="<?=$gallery['custom_gallery_id']?>">
                <i class="fa fa-trash" aria-hidden="true"></i>
                </div>
            <?php } else { ?>
            <div class="custom_galleryimageupload" data-gallery="<?=$gallery['custom_gallery_id']?>" id="newgalleryimg_<?=getuploadid()?>"></div>
        </div>
        <?php } ?>
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
<div class="content-row">
    <div class="add_new_gallery">Add New Example</div>
</div>

