<div class="galleryinfotitle">
    <div class="displaygallery show">
        <i class="fa fa-chevron-down" aria-hidden="true"></i>
    </div>
    <div class="title">GALLERY:</div>
</div>
<div class="custom_galleries_area">
    <?php foreach ($galleries as $gallery) { ?>
        <div class="content-row">
            <div class="label custom_gallerytitle">Gallery <?=$gallery['numpp']?> Title:</div>
            <input class="custom_gallerytitle" readonly="readonly" name="gallery_name" data-gallery="<?=$gallery['custom_gallery_id']?>" value="<?=$gallery['gallery_name']?>"/>
            <div class="gallery_collageimage">
                <?php if (!empty($gallery['gallery_image'])) { ?>
                    <img src="<?=$gallery['gallery_image']?>" alt="Gallery Image"/>
                <?php } else { ?>
                    &nbsp;
                <?php } ?>
            </div>
            <div class="custom_imagesubtitle">click image to enlarge<br/> (366px x 244px)</div>
        </div>
        <div class="content-row">
            <div class="custom_imagesubtitlesize"></div>
        </div>
    <?php } ?>
</div>
