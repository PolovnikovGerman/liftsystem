<div class="mainimage_area">
    <div class="content-row">
        <div class="imagetitle <?=empty($item['main_image']) ? 'emptytitle' : ''?>">Main:</div>
    </div>
    <div class="content-row mainimage_view">
        <div class="previewimage mainimage <?=empty($item['main_image']) ? 'emptypreview' : ''?>">
            <?php if (!empty($item['main_image'])) { ?>
                <img src="<?=$item['main_image']?>" alt="Main Image"/>
            <?php } ?>
        </div>
    </div>
</div>
<div class="categoryimage_area">
    <div class="content-row">
        <div class="imagetitle <?=empty($item['category_image']) ? 'emptytitle' : ''?>">Category Page:</div>
    </div>
    <div class="content-row categoryimage_view">
        <div class="previewimage categoryimage <?=empty($item['category_image']) ? 'emptypreview' : ''?>">
            <?php if (!empty($item['category_image'])) { ?>
                <img src="<?=$item['category_image']?>" alt="Category Image"/>
            <?php } ?>
        </div>
    </div>
</div>
<div class="topbannerimage_area">
    <div class="content-row">
        <div class="imagetitle <?=empty($item['top_banner']) ? 'emptytitle' : ''?>">Special Top Banner:</div>
    </div>
    <div class="content-row topbannerimage_view">
        <div class="previewimage topbannerimage <?=empty($item['top_banner']) ? 'emptypreview' : ''?>">
            <?php if (!empty($item['top_banner'])) { ?>
                <img src="<?=$item['top_banner']?>" alt="Top Banner Image"/>
            <?php } ?>
        </div>
    </div>
</div>