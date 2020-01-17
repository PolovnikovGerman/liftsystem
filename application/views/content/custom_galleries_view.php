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
            <div class="custom_imagesubtitle">click image to enlarge</div>
        </div>
        <div class="content-row">
            <div class="custom_imagesubtitlesize">(450px x 450px)</div>
        </div>
        <div class="content-row galleryitemssources">
            <?php foreach ($gallery['items'] as $item) { ?>
                <div class="custom_galleryitem" data-item="<?=$item['custom_galleryitem_id']?>" data-src="<?=$item['item_source']?>" data-fancybox="gallery_<?=$gallery['custom_gallery_id']?>">
                    <img src="<?=$item['item_source']?>" alt="Galley Item"/>
                </div>
            <?php } ?>
            <?php for ($i=$gallery['count_items']; $i<$maxitems; $i++) { ?>
                <div class="custom_emptygalleryitem" data-item="<?=$i*(-1)?>">&nbsp;</div>
            <?php } ?>
        </div>
    <?php } ?>
</div>
