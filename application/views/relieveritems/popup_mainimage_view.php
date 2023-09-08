<div class="mainimage_area">
    <div class="content-row">
        <div class="imagetitle <?=empty($item['main_image']) ? 'emptytitle' : ''?>">Main:</div>
    </div>
    <div class="content-row mainimage_view">
        <?php if (empty($item['main_image'])) { ?>
            <div class="emptyimage mainimage">&nbsp;</div>
        <?php } else { ?>
            <div class="previewimage mainimage">
                <img src="<?=$item['main_image']?>" alt="Main Image"/>
            </div>
        <?php } ?>
    </div>
</div>
<div class="categoryimage_area">
    <div class="content-row">
        <div class="imagetitle <?=empty($item['category_image']) ? 'emptytitle' : ''?>">Category Page:</div>
    </div>
    <div class="content-row categoryimage_view">
        <?php if (empty($item['category_image'])) { ?>
            <div class="emptyimage categoryimage">&nbsp;</div>
        <?php } else { ?>
            <div class="previewimage categoryimage">
                <img src="<?=$item['category_image']?>" alt="Category Image"/>
            </div>
        <?php } ?>
    </div>
</div>
<div class="topbannerimage_area">
    <div class="content-row">
        <div class="imagetitle <?=empty($item['top_banner']) ? 'emptytitle' : ''?>">Special Top Banner:</div>
    </div>
    <div class="content-row topbannerimage_view">
        <?php if (empty($item['top_banner'])) { ?>
            <div class="emptyimage topbannerimage">&nbsp;</div>
        <?php } else { ?>
            <div class="previewimage topbannerimage">
                <img src="<?=$item['top_banner']?>" alt="Top Banner Image"/>
            </div>
        <?php } ?>
    </div>
</div>