<?php foreach ($galleries as $gallery) { ?>
    <div class="content-row">
    <div class="label custom_gallerytitle">Gallery <?=$gallery['numpp']?> Title:</div>
    <input class="custom_gallerytitle" data-content="gallery" data-field="gallery_name" data-gallery="<?=$gallery['custom_gallery_id']?>" value="<?=$gallery['gallery_name']?>"/>
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
    <div class="custom_imagesubtitle">click image to enlarge<br/> (366px x 244px)</div>
    <div class="label custom_galleryshow_label">Show Gallery </div>
    <div class="custom_galleryshow">
        <label class="switch">
            <input type="checkbox" data-content="gallery" data-field="gallery_show" data-gallery="<?=$gallery['custom_gallery_id']?>" <?=$gallery['gallery_show']==1 ? 'checked="checked"' : ''?> />
            <span class="slider round"></span>
        </label>
    </div>
    <div class="custom_gallerydelete" data-gallery="<?=$gallery['custom_gallery_id']?>">
        <i class="fa fa-times-circle" aria-hidden="true" title="Remove Gallery"></i>
    </div>
    </div>
<?php } ?>
<div class="content-row">
    <div class="add_new_gallery">Add New Gallery</div>
</div>

