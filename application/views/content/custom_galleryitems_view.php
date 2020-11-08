<?php $numr = 0; ?>
<?php foreach ($galleryitems as $item) { ?>
    <?php if ($numr==0) { ?>
        <div class="content-row">
    <?php } ?>
    <div class="custom_galleryitemdata">
         <div class="custom_galleryitem" data-src="<?=$item['item_source']?>" data-fancybox="gallery_main">
            <img src="<?=$item['item_source']?>" alt="'Item"/>
        </div>
        <div class="custom_galleryitemtxt" title="<?=$item['item_description']?>">
            <?=$item['item_description']?>
        </div>
    </div>
    <?php $numr++;?>
    <?php if ($numr==6) { ?>
        </div>
        <?php $numr = 0;?>
    <?php } ?>
<?php } ?>
<?php if ($numr >0 ) { ?>
    </div>
<?php } ?>