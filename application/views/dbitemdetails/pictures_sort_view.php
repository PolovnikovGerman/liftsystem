<div class="imagesortcontent" id="imagesortcontent">
    <?php $nrow = 0;?>
    <?php foreach ($images as $image) { ?>
        <div class="itemimagesort" data-idx="<?=$image['item_img_id']?>">
            <img src="<?=$image['item_img_name']?>" alt="Img"/>
        </div>
    <?php } ?>
</div>
<div class="imagesortrow">
    <div class="savesort">Save Sequence</div>
</div>
