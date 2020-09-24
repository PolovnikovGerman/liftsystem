<?php $numr = 0; ?>
<?php foreach ($galleryitems as $item) { ?>
    <?php if ($numr==0) { ?>
        <div class="content-row">
    <?php } ?>
    <div class="custom_galleryitem" data-item="<?=$item['custom_galleryitem_id']?>" data-src="<?=$item['item_source']?>" data-fancybox="gallery_main">
        <img src="<?=$item['item_source']?>" alt="'Item"/>
    </div>
    <div class="custom_galleryitemdelete" data-item="<?=$item['custom_galleryitem_id']?>">
        <i class="fa fa-trash" aria-hidden="true"></i>
    </div>
    <?php $numr++;?>
    <?php if ($numr==3) { ?>
        </div>
        <?php $numr = 0;?>
    <?php } ?>
<?php } ?>
<?php if ($numr >0 ) { ?>
    </div>
<?php } ?>
<div class="content-row">
    <div class="add_new_gallery" id="add_new_gallery">Add New Gallery Pic</div>
</div>

