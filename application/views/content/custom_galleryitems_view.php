<?php $numr = 0; ?>
<?php foreach ($galleryitems as $item) { ?>
    <?php if ($numr==0) { ?>
        <div class="content-row">
    <?php } ?>
    <div class="custom_galleryitem">
        <img src="<?=$item['item_source']?>" alt="'Item" data-fancybox="gallery_main"/>
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