<div class="popupimages_section itemimagesection <?=$mode=='view' ? 'viewmode' : ''?>">
    <?=$main_view?>
</div>
<div class="popupimages_section addlimagesection <?=$mode=='view' ? 'viewmode' : ''?>">
    <?=$add_view?>
</div>
<?php if ($colorview) { ?>
    <div class="popupimages_section optionimagesection <?=$mode=='view' ? 'viewmode' : ''?>">
        <?=$options_view?>
    </div>
<?php } ?>
