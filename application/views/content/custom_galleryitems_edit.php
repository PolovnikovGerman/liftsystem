<?php foreach ($galleries as $gallery) { ?>
    <div class="content-row">
        <div class="label custom_gallerytitle">Gallery <?=$gallery['numpp']?> Title:</div>
        <input class="custom_gallerytitle" data-content="gallery" data-field="gallery_name" data-gallery="<?=$gallery['custom_gallery_id']?>" value="<?=$gallery['gallery_name']?>"/>
        <div class="custom_imagesubtitle">click image to enlarge</div>
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
    <div class="content-row">
        <div class="custom_imagesubtitlesize">(450px x 450px)</div>
    </div>
    <div class="content-row galleryitemssources">
        <?php foreach ($gallery['items'] as $item) { ?>
            <div class="custom_galleryitem" data-item="<?=$item['custom_galleryitem_id']?>" data-src="<?=$item['item_source']?>" data-fancybox="gallery_<?=$gallery['custom_gallery_id']?>">
                <img src="<?=$item['item_source']?>" alt="Galley Item"/>
            </div>
            <div class="custom_galleryitemdelete" data-item="<?=$item['custom_galleryitem_id']?>" data-gallery="<?=$gallery['custom_gallery_id']?>">
                <i class="fa fa-trash" aria-hidden="true"></i>
            </div>
        <?php } ?>
        <?php for ($i=$gallery['count_items']; $i<$maxitems; $i++) { ?>
            <div class="custom_emptygalleryitem" data-item="<?=($i+1)*(-1)?>" data-gallery="<?=$gallery['custom_gallery_id']?>">
                <div class="custom_galleryitemupload" id="newgalleryimg_<?=getuploadid()?>"></div>
                &nbsp;</div>
        <?php } ?>
    </div>
<?php } ?>
<div class="content-row">
    <div class="add_new_gallery">Add New Gallery</div>
</div>
